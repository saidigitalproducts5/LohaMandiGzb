<?php

namespace App\Controllers\Account;

use App\Models\CrmCustomerModel;
use App\Models\CrmDealModel;
use App\Models\CrmFollowupModel;
use App\Models\CrmMeetingModel;
use App\Models\CrmNoteModel;
use App\Models\CrmTaskModel;
use App\Models\SellerModel;

/**
 * CRM — seller-side customer relationship management.
 * Customer database, sales pipeline, follow-ups, tasks, notes, meetings.
 */
class Crm extends AccountBase
{
    protected $seller = null;

    private function requireSeller(): ?array
    {
        if ($this->uRole() !== 'seller') {
            $this->flash('error', 'CRM is available for sellers.');
            return null;
        }

        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return null;
        }

        return $seller;
    }

    /**
     * GET /account/crm — dashboard: pipeline board, upcoming follow-ups, open tasks.
     */
    public function index()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $dealModel = new CrmDealModel();
        $board = $dealModel->byStage($sid);

        $customers = (new CrmCustomerModel())->summary($sid);
        $tasks = (new CrmTaskModel())->forSeller($sid, 'open');
        $followups = (new CrmFollowupModel())->forSeller($sid, 'pending');
        $meetings = (new CrmMeetingModel())->forSeller($sid, 'scheduled');

        // Pipeline value summary (live deals only).
        $live = [];
        foreach (['prospecting', 'qualified', 'quotation', 'negotiation'] as $s) {
            $live = array_merge($live, $board[$s]);
        }
        $pipelineValue = array_sum(array_column($live, 'value'));

        return $this->render('account/crm_dashboard', [
            'board'          => $board,
            'stages'         => CrmDealModel::STAGES,
            'customers'      => $customers,
            'tasks'          => $tasks,
            'followups'      => $followups,
            'meetings'       => $meetings,
            'customerCount'  => count($customers),
            'openTaskCount'  => (new CrmTaskModel())->openCount($sid),
            'followupCount'  => (new CrmFollowupModel())->pendingCount($sid),
            'meetingCount'   => (new CrmMeetingModel())->upcomingCount($sid),
            'pipelineValue'  => $pipelineValue,
            'wonValue'       => array_sum(array_column($board['won'], 'value')),
            'activeNav'     => 'crm',
        ], 'CRM Dashboard');
    }

    /**
     * GET /account/crm/customers — customer database.
     */
    public function customers()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $q = trim((string) $this->request->getGet('q'));
        $customers = (new CrmCustomerModel())->summary($sid, $q);

        return $this->render('account/crm_customers', [
            'customers' => $customers,
            'q'         => $q,
            'activeNav' => 'crm',
        ], 'CRM Customers');
    }

    /**
     * Customer detail with deals, tasks, notes, follow-ups.
     */
    public function customerShow(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $customer = (new CrmCustomerModel())->findForSeller($sid, $id);
        if ($customer === null) {
            return $this->notFound('Customer not found.');
        }
        $deals = (new CrmDealModel())->forSeller($sid);
        $deals = array_values(array_filter($deals, fn($d) => (int) $d['customer_id'] === $id));
        $tasks = array_values(array_filter((new CrmTaskModel())->forSeller($sid), fn($t) => (int) $t['customer_id'] === $id));
        $followups = array_values(array_filter((new CrmFollowupModel())->forSeller($sid), fn($f) => (int) $f['customer_id'] === $id));
        $meetings = array_values(array_filter((new CrmMeetingModel())->forSeller($sid), fn($m) => (int) $m['customer_id'] === $id));
        $notes = (new CrmNoteModel())->forSeller($sid, $id);

        $totalWon = array_sum(array_column(array_filter($deals, fn($d) => $d['stage'] === 'won'), 'value'));

        return $this->render('account/crm_customer_view', [
            'customer'  => $customer,
            'deals'     => $deals,
            'tasks'     => $tasks,
            'followups' => $followups,
            'meetings'  => $meetings,
            'notes'     => $notes,
            'stages'    => CrmDealModel::STAGES,
            'channels'  => CrmFollowupModel::CHANNELS,
            'totalWon'  => $totalWon,
            'activeNav' => 'crm',
        ], 'Customer: ' . $customer['name']);
    }

    /**
     * GET/POST create customer.
     */
    public function customerCreate()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        if ($this->request->getMethod() === 'POST') {
            $crmCustomerModel = new CrmCustomerModel();
            $data = $this->collectInput();
            if (trim((string) $data['name']) === '') {
                $this->flash('error', 'Customer name is required.');
                return redirect()->to('account/crm/customers');
            }
            $data['seller_id'] = $sid;
            $crmCustomerModel->insert($data);

            $this->flash('success', 'Customer added.');
            return redirect()->to('account/crm/customers');
        }

        return $this->render('account/crm_customer_form', [
            'customer' => null,
            'activeNav' => 'crm',
            'isEdit'   => false,
        ], 'Add Customer');
    }

    /**
     * GET/POST edit customer.
     */
    public function customerEdit(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $crmCustomerModel = new CrmCustomerModel();
        $customer = $crmCustomerModel->findForSeller($sid, $id);
        if ($customer === null) {
            return $this->notFound('Customer not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->collectInput();
            if (trim((string) $data['name']) === '') {
                $this->flash('error', 'Customer name is required.');
                return redirect()->to('account/crm/customers');
            }
            $crmCustomerModel->update((int) $customer['id'], $data);
            $this->flash('success', 'Customer updated.');

            return redirect()->to('account/crm/customers/' . (int) $customer['id']);
        }

        return $this->render('account/crm_customer_form', [
            'customer' => $customer,
            'activeNav' => 'crm',
            'isEdit'  => true,
        ], 'Edit Customer');
    }

    public function customerDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $customer = (new CrmCustomerModel())->findForSeller($sid, $id);
        if ($customer === null) {
            return $this->notFound('Customer not found.');
        }

        (new CrmCustomerModel())->delete((int) $customer['id']);
        $this->flash('success', 'Customer deleted.');

        return redirect()->to('account/crm/customers');
    }

    /**
     * GET/POST deal (create a sales pipeline entry).
     */
    public function dealCreate(?int $customerId = null)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $customers = (new CrmCustomerModel())->forSeller($sid);

        if ($this->request->getMethod() === 'POST') {
            $dealModel = new CrmDealModel();
            $data = $this->dealInput($sid);
            if (trim((string) $data['title']) === '') {
                $this->flash('error', 'Deal title is required.');
                return redirect()->to('account/crm/deals');
            }
            $dealModel->insert($data);
            $this->flash('success', 'Deal created.');
            return redirect()->to('account/crm/deals');
        }

        return $this->render('account/crm_deal_form', [
            'customers' => $customers,
            'deal'      => null,
            'stages'    => CrmDealModel::STAGES,
            'isEdit'    => false,
            'activeNav' => 'crm',
            'presetCustomer' => $customerId,
        ], 'New Deal');
    }

    public function dealEdit(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $dealModel = new CrmDealModel();
        $deal = $dealModel->findForSeller($sid, $id);
        if ($deal === null) {
            return $this->notFound('Deal not found.');
        }

        $customers = (new CrmCustomerModel())->forSeller($sid);

        if ($this->request->getMethod() === 'POST') {
            $data = $this->dealInput($sid);
            if (trim((string) $data['title']) === '') {
                $this->flash('error', 'Deal title is required.');
                return redirect()->to('account/crm/deals');
            }
            $dealModel->update((int) $deal['id'], $data);
            $this->flash('success', 'Deal updated.');
            return redirect()->to('account/crm/deals');
        }

        return $this->render('account/crm_deal_form', [
            'customers' => $customers,
            'deal'      => $deal,
            'stages'    => CrmDealModel::STAGES,
            'isEdit'    => true,
            'activeNav' => 'crm',
        ], 'Edit Deal');
    }

    /**
     * Update deal stage (pipeline drag / quick change).
     */
    public function dealStage(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $dealModel = new CrmDealModel();
        $deal = $dealModel->findForSeller($sid, $id);
        if ($deal === null) {
            return $this->notFound('Deal not found.');
        }

        $stage = trim((string) $this->request->getPost('stage'));
        if (!array_key_exists($stage, CrmDealModel::STAGES)) {
            $this->flash('error', 'Invalid stage.');
        } else {
            $dealModel->update($deal['id'], ['stage' => $stage]);
            $this->flash('success', 'Deal moved to ' . CrmDealModel::STAGES[$stage] . '.');
        }

        return redirect()->back();
    }

    public function dealDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $deal = (new CrmDealModel())->findForSeller($sid, $id);
        if ($deal === null) {
            return $this->notFound('Deal not found.');
        }

        (new CrmDealModel())->delete((int) $deal['id']);
        $this->flash('success', 'Deal deleted.');
        return redirect()->to('account/crm/deals');
    }

    /**
     * GET deals, with optional stage filter. Called directly or from a redirect.
     */
    public function deals(?string $stage = null)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $stage = trim((string) ($stage ?? $this->request->getGet('stage')));
        $deals = (new CrmDealModel())->forSeller($sid, $stage !== '' ? $stage : null);

        return $this->render('account/crm_deals', [
            'deals'     => $deals,
            'stage'     => $stage,
            'stages'    => CrmDealModel::STAGES,
            'activeNav' => 'crm',
        ], 'Deals');
    }

    /**
     * Tasks — list + add + toggle.
     */
    public function tasks()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $status = trim((string) $this->request->getGet('status'));

        if ($this->request->getMethod() === 'POST') {
            $taskModel = new CrmTaskModel();
            $data = $this->taskInput($sid);
            if (trim((string) $data['title']) === '') {
                $this->flash('error', 'Task title is required.');
                return redirect()->to('account/crm/tasks');
            }
            $taskModel->insert($data);
            $this->flash('success', 'Task added.');
            return redirect()->to('account/crm/tasks');
        }

        $tasks = (new CrmTaskModel())->forSeller($sid, $status !== '' ? $status : null);

        return $this->render('account/crm_tasks', [
            'tasks'     => $tasks,
            'status'    => $status,
            'categories' => CrmTaskModel::CATEGORIES,
            'priorities' => CrmTaskModel::PRIORITIES,
            'customers' => (new CrmCustomerModel())->forSeller($sid),
            'deals'     => (new CrmDealModel())->forSeller($sid),
            'activeNav' => 'crm',
        ], 'Tasks');
    }

    public function taskToggle(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $task = (new CrmTaskModel())->findForSeller($sid, $id);
        if ($task === null) {
            return $this->notFound('Task not found.');
        }

        (new CrmTaskModel())->update((int) $task['id'], [
            'status' => $task['status'] === 'done' ? 'open' : 'done',
        ]);
        $this->flash('success', 'Task updated.');

        return redirect()->back();
    }

    public function taskDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $task = (new CrmTaskModel())->findForSeller($sid, $id);
        if ($task === null) {
            return $this->notFound('Task not found.');
        }

        (new CrmTaskModel())->delete((int) $task['id']);
        $this->flash('success', 'Task deleted.');

        return redirect()->back();
    }

    /**
     * Follow-ups — list + add + mark done.
     */
    public function followups()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $status = trim((string) $this->request->getGet('status'));

        if ($this->request->getMethod() === 'POST') {
            $followupModel = new CrmFollowupModel();
            $data = $this->followupInput($sid);
            $followupModel->insert($data);
            $this->flash('success', 'Follow-up scheduled.');
            return redirect()->to('account/crm/followups');
        }

        $followups = (new CrmFollowupModel())->forSeller($sid, $status !== '' ? $status : null);

        return $this->render('account/crm_followups', [
            'followups' => $followups,
            'status'    => $status,
            'channels'  => CrmFollowupModel::CHANNELS,
            'customers' => (new CrmCustomerModel())->forSeller($sid),
            'deals'     => (new CrmDealModel())->forSeller($sid),
            'activeNav' => 'crm',
        ], 'Follow-ups');
    }

    public function followupComplete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $followup = (new CrmFollowupModel())->findForSeller($sid, $id);
        if ($followup === null) {
            return $this->notFound('Follow-up not found.');
        }

        (new CrmFollowupModel())->update((int) $followup['id'], [
            'status'  => 'done',
            'done_at' => date('Y-m-d H:i:s'),
        ]);
        $this->flash('success', 'Follow-up completed.');

        return redirect()->back();
    }

    public function followupDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $followup = (new CrmFollowupModel())->findForSeller($sid, $id);
        if ($followup === null) {
            return $this->notFound('Follow-up not found.');
        }

        (new CrmFollowupModel())->delete((int) $followup['id']);
        $this->flash('success', 'Follow-up deleted.');

        return redirect()->back();
    }

    /**
     * Meetings — list + add.
     */
    public function meetings()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $status = trim((string) $this->request->getGet('status'));

        if ($this->request->getMethod() === 'POST') {
            $meetingModel = new CrmMeetingModel();
            $data = $this->meetingInput($sid);
            if (trim((string) $data['title']) === '') {
                $this->flash('error', 'Meeting title is required.');
                return redirect()->to('account/crm/meetings');
            }
            $meetingModel->insert($data);
            $this->flash('success', 'Meeting scheduled.');
            return redirect()->to('account/crm/meetings');
        }

        $meetings = (new CrmMeetingModel())->forSeller($sid, $status !== '' ? $status : null);

        return $this->render('account/crm_meetings', [
            'meetings'  => $meetings,
            'status'    => $status,
            'statuses'  => CrmMeetingModel::STATUSES,
            'customers' => (new CrmCustomerModel())->forSeller($sid),
            'deals'     => (new CrmDealModel())->forSeller($sid),
            'activeNav' => 'crm',
        ], 'Meetings');
    }

    public function meetingStatus(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $meeting = (new CrmMeetingModel())->findForSeller($sid, $id);
        if ($meeting === null) {
            return $this->notFound('Meeting not found.');
        }

        $status = trim((string) $this->request->getPost('status'));
        if (!in_array($status, CrmMeetingModel::STATUSES, true)) {
            $this->flash('error', 'Invalid status.');
        } else {
            (new CrmMeetingModel())->update((int) $meeting['id'], ['status' => $status]);
            $this->flash('success', 'Meeting updated.');
        }

        return redirect()->back();
    }

    public function meetingDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $meeting = (new CrmMeetingModel())->findForSeller($sid, $id);
        if ($meeting === null) {
            return $this->notFound('Meeting not found.');
        }

        (new CrmMeetingModel())->delete((int) $meeting['id']);
        $this->flash('success', 'Meeting deleted.');

        return redirect()->back();
    }

    /**
     * Add a note to a customer.
     */
    public function noteCreate()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $customerId = (int) $this->request->getPost('customer_id');
        $body = trim((string) $this->request->getPost('body'));

        $customer = $customerId > 0
            ? (new CrmCustomerModel())->findForSeller($sid, $customerId)
            : null;
        if ($customer === null) {
            return $this->notFound('Customer not found.');
        }
        if ($body === '') {
            $this->flash('error', 'Note is empty.');
            return redirect()->to('account/crm/customers/' . $customerId);
        }

        (new CrmNoteModel())->insert([
            'seller_id'   => $sid,
            'customer_id' => $customerId,
            'body'        => $body,
        ]);
        $this->flash('success', 'Note added.');

        return redirect()->to('account/crm/customers/' . $customerId);
    }

    public function noteDelete(int $id)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $note = (new CrmNoteModel())->find($id);
        if ($note === null || (int) $note['seller_id'] !== $sid) {
            return $this->notFound('Note not found.');
        }

        (new CrmNoteModel())->delete((int) $note['id']);
        $this->flash('success', 'Note deleted.');

        return redirect()->back();
    }

    /* ----------------------------------------------------------------- */

    private function collectInput(): array
    {
        return [
            'name'    => trim((string) $this->request->getPost('name')),
            'company' => trim((string) $this->request->getPost('company')) ?: null,
            'email'   => trim((string) $this->request->getPost('email')) ?: null,
            'mobile'  => trim((string) $this->request->getPost('mobile')) ?: null,
            'address' => trim((string) $this->request->getPost('address')) ?: null,
            'city'    => trim((string) $this->request->getPost('city')) ?: null,
            'source'  => $this->request->getPost('source') ?: 'manual',
            'notes'   => trim((string) $this->request->getPost('notes')) ?: null,
        ];
    }

    private function dealInput(int $sellerId): array
    {
        $customerId = (int) $this->request->getPost('customer_id');
        return [
            'seller_id'      => $sellerId,
            'customer_id'    => $customerId > 0 ? $customerId : null,
            'title'          => trim((string) $this->request->getPost('title')),
            'value'          => (float) ($this->request->getPost('value') ?: 0),
            'stage'          => $this->request->getPost('stage') ?: 'prospecting',
            'expected_close' => trim((string) $this->request->getPost('expected_close')) ?: null,
            'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
        ];
    }

    private function taskInput(int $sellerId): array
    {
        $customerId = (int) $this->request->getPost('customer_id');
        $dealId     = (int) $this->request->getPost('deal_id');
        $dueAt      = trim((string) $this->request->getPost('due_at'));

        return [
            'seller_id'   => $sellerId,
            'customer_id' => $customerId > 0 ? $customerId : null,
            'deal_id'     => $dealId > 0 ? $dealId : null,
            'title'       => trim((string) $this->request->getPost('title')),
            'category'    => $this->request->getPost('category') ?: 'task',
            'due_at'      => $dueAt !== '' ? date('Y-m-d H:i:s', strtotime($dueAt)) : null,
            'priority'    => $this->request->getPost('priority') ?: 'medium',
            'status'      => 'open',
        ];
    }

    private function followupInput(int $sellerId): array
    {
        $customerId = (int) $this->request->getPost('customer_id');
        $dealId     = (int) $this->request->getPost('deal_id');
        $scheduled  = trim((string) $this->request->getPost('scheduled_at'));

        return [
            'seller_id'    => $sellerId,
            'customer_id'  => $customerId > 0 ? $customerId : null,
            'deal_id'      => $dealId > 0 ? $dealId : null,
            'channel'      => $this->request->getPost('channel') ?: 'call',
            'scheduled_at' => $scheduled !== '' ? date('Y-m-d H:i:s', strtotime($scheduled)) : null,
            'note'         => trim((string) $this->request->getPost('note')) ?: null,
            'status'       => 'pending',
        ];
    }

    private function meetingInput(int $sellerId): array
    {
        $customerId = (int) $this->request->getPost('customer_id');
        $dealId     = (int) $this->request->getPost('deal_id');
        $starts     = trim((string) $this->request->getPost('starts_at'));
        $ends       = trim((string) $this->request->getPost('ends_at'));

        return [
            'seller_id'    => $sellerId,
            'customer_id'  => $customerId > 0 ? $customerId : null,
            'deal_id'      => $dealId > 0 ? $dealId : null,
            'title'        => trim((string) $this->request->getPost('title')),
            'starts_at'    => $starts !== '' ? date('Y-m-d H:i:s', strtotime($starts)) : null,
            'ends_at'      => $ends !== '' ? date('Y-m-d H:i:s', strtotime($ends)) : null,
            'location'     => trim((string) $this->request->getPost('location')) ?: null,
            'notes'        => trim((string) $this->request->getPost('notes')) ?: null,
            'status'       => 'scheduled',
        ];
    }

    private function notFound(string $message)
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($message);
    }
}