<?php

namespace App\Controllers\Account;

use App\Models\ChatModel;
use App\Models\MessageModel;
use App\Models\NotificationModel;
use App\Models\SellerModel;

class Chats extends AccountBase
{
    public function index()
    {
        $isSeller = $this->uRole() === 'seller';
        $seller = null;
        $pickSellers = [];

        if ($isSeller) {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account');
            }
            $rows = (new ChatModel())->sellerChats((int) $seller['id'], $this->uid());

            // Community sellers the dropdown offers (exclude myself).
            $pickSellers = (new SellerModel())->activeSellersForPickup((int) $seller['id']);
        } else {
            $rows = (new ChatModel())->buyerChats($this->uid());
        }

        return $this->render('account/chats', [
            'rows'        => $rows,
            'isSeller'    => $isSeller,
            'pickSellers' => $pickSellers,
            'activeNav'   => 'chats',
        ], 'Chats');
    }

    /**
     * Seller/buyer picker page to start a new chat.
     */
    public function newChat()
    {
        $isSeller = $this->uRole() === 'seller';
        $excludeId = 0;

        if ($isSeller) {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account');
            }
            $excludeId = (int) $seller['id'];
        }

        $sellers = (new SellerModel())->activeSellersForPickup($excludeId);

        return $this->render('account/chats_new', [
            'sellers'    => $sellers,
            'isSeller'   => $isSeller,
            'activeNav'  => 'chats',
        ], 'New Chat');
    }

    /**
     * Opens (or continues) a chat with a seller. seller_id = sellers.id.
     * Buyers chat about products; sellers chat seller-to-seller.
     */
    public function open($sellerId, ?int $productId = null)
    {
        $seller = (new SellerModel())->find((int) $sellerId);
        if ($seller === null || $seller['status'] !== 'active') {
            $this->flash('error', 'Seller not found.');
            return redirect()->back();
        }

        if ($this->uRole() === 'seller') {
            $me = (new SellerModel())->findByUser($this->uid());
            if ($me === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account/chats');
            }
            if ((int) $me['id'] === (int) $seller['id']) {
                $this->flash('error', 'You cannot start a chat with yourself.');
                return redirect()->to('account/chats');
            }
            // Seller-to-seller thread: seller_id = other seller, buyer_id = my user id.
            $chatId = (new ChatModel())->open((int) $seller['id'], $this->uid());

            return redirect()->to('account/chats/' . $chatId);
        }

        $chatId = (new ChatModel())->open((int) $seller['id'], $this->uid(), $productId);

        return redirect()->to('account/chats/' . $chatId);
    }

    /**
     * Chat thread view + send message.
     */
    public function show($id)
    {
        $chatId = (int) $id;
        $chat = (new ChatModel())->show($chatId);
        if ($chat === null) {
            $this->flash('error', 'Chat not found.');
            return redirect()->to('account/chats');
        }

        $isSeller = $this->uRole() === 'seller';
        $isIncoming = false;

        // Access control.
        if ($isSeller) {
            $me = (new SellerModel())->findByUser($this->uid());
            if ($me === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account/chats');
            }

            $isIncoming = (int) $chat['seller_id'] === (int) $me['id'];
            $isOutgoing = (int) $chat['buyer_id'] === $this->uid();

            if (!$isIncoming && !$isOutgoing) {
                $this->flash('error', 'You cannot view this chat.');
                return redirect()->to('account/chats');
            }

            if ($isIncoming) {
                // Other side: a buyer, or another community seller who messaged me.
                $otherSeller = db_connect()->table('sellers')
                    ->select('business_name')->where('user_id', (int) $chat['buyer_id'])
                    ->get()->getRowArray();
                if ($otherSeller) {
                    $partyName = $otherSeller['business_name'];
                } else {
                    $other = db_connect()->table('users')->where('id', (int) $chat['buyer_id'])->get()->getRowArray();
                    $partyName = $other['name'] ?? 'Buyer';
                }
            } else {
                $other = db_connect()->table('sellers')->where('id', (int) $chat['seller_id'])->get()->getRowArray();
                $partyName = $other['business_name'] ?? 'Seller';
            }
        } else {
            if ((int) $chat['buyer_id'] !== $this->uid()) {
                $this->flash('error', 'You cannot view this chat.');
                return redirect()->to('account/chats');
            }
            $seller = db_connect()->table('sellers')->where('id', $chat['seller_id'])->get()->getRowArray();
            $partyName = $seller['business_name'] ?? 'Seller';
        }

        $model = new MessageModel();

        if ($this->request->getMethod() === 'POST') {
            $body = trim((string) $this->request->getPost('message'));
            if ($body !== '') {
                $model->add($chatId, $this->uid(), $body);
                (new ChatModel())->touch($chatId, $body);

                // Notify the other party (never myself).
                if ($isSeller) {
                    $notifyUserId = $isIncoming
                        ? (int) $chat['buyer_id']
                        : (int) (db_connect()->table('sellers')->where('id', (int) $chat['seller_id'])->get()->getRow()->user_id ?? 0);
                } else {
                    $notifyUserId = (int) (db_connect()->table('sellers')->where('id', $chat['seller_id'])->get()->getRow()->user_id ?? 0);
                }
                if ($notifyUserId && $notifyUserId !== $this->uid()) {
                    (new NotificationModel())->push(
                        $notifyUserId,
                        'New message from ' . $this->uName(),
                        mb_substr($body, 0, 120),
                        'chat',
                        $chatId
                    );
                }
            }

            return redirect()->to('account/chats/' . $chatId);
        }

        // Mark the other party's messages as read on view.
        $model->markRead($chatId, $this->uid());
        $messages = $model->forChat($chatId);

        return $this->render('account/chat_view', [
            'chat'      => $chat,
            'messages'  => $messages,
            'partyName' => $partyName,
            'isSeller'  => $isSeller,
            'activeNav' => 'chats',
        ], 'Chat');
    }
}