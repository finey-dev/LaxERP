<?php

namespace Workdo\BudgetPlanner\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BudgetPlanner\Entities\Budget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\Account\Entities\Bill;
use Workdo\Account\Entities\Payment;
use Workdo\Account\Entities\Revenue;
use Workdo\BudgetPlanner\DataTables\BudgetPlannerDataTable;
use Workdo\BudgetPlanner\Events\CreateBudgetPlan;
use Workdo\BudgetPlanner\Events\DestroyBudgetPlan;
use Workdo\BudgetPlanner\Events\UpdateBudgetPlan;
use Workdo\ProductService\Entities\Category;

class BudgetPlannerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(BudgetPlannerDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('budget plan manage')) {
            $periods = Budget::$period;
            return $dataTable->render('budget-planner::budget.index', compact('periods'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('budget plan create')) {
            $periods = Budget::$period;

            $data['monthList'] = $month = $this->yearMonth();    //Monthly

            $data['quarterly_monthlist'] =
                [
                    //Quarterly
                    'Jan-Mar',
                    'Apr-Jun',
                    'Jul-Sep',
                    'Oct-Dec',
                ];

            $data['half_yearly_monthlist'] =
                [   // Half - Yearly
                    'Jan-Jun',
                    'Jul-Dec',
                ];

            $data['yearly_monthlist'] =
                [
                    // Yearly
                    'Jan-Dec',
                ];

            $data['yearList'] = $this->yearList();

            $incomeproduct  = Category::where('created_by', creatorId())->where('type', '=', 1)->where('workspace_id', getActiveWorkSpace())->get();
            $expenseproduct = Category::where('created_by', creatorId())->where('type', '=', 2)->where('workspace_id', getActiveWorkSpace())->get();


            return view('budget-planner::budget.create', compact('periods', 'incomeproduct', 'expenseproduct'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function yearMonth()
    {
        $month[] = 'January';
        $month[] = 'February';
        $month[] = 'March';
        $month[] = 'April';
        $month[] = 'May';
        $month[] = 'June';
        $month[] = 'July';
        $month[] = 'August';
        $month[] = 'September';
        $month[] = 'October';
        $month[] = 'November';
        $month[] = 'December';

        return $month;
    }

    public function yearList()
    {
        $starting_year = date('Y', strtotime('-5 year'));
        $ending_year   = date('Y');

        foreach (range($ending_year, $starting_year) as $year) {
            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('budget plan create')) {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:120',
                'period' => 'required',


            ]);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $budget               = new Budget();
            $budget->name         = $request->name;
            $budget->from         = $request->year;
            $budget->period       = $request->period;
            $budget->income_data  = json_encode($request->income);
            $budget->expense_data = json_encode($request->expense);
            $budget->created_by   = creatorId();
            $budget->workspace    = getActiveWorkSpace();
            $budget->save();

            event(new CreateBudgetPlan($request, $budget));

            return redirect()->route('budget.index')->with('success', __('The budget plan has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if (Auth::user()->isAbleTo('budget plan show')) {
            try {
                $id       = Crypt::decrypt($id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Budget Not Found.'));
            }

            $budget                = Budget::find($id);
            $budget['income_data'] = json_decode($budget->income_data, true);
            $budgetTotalArrs       = !empty($budget['income_data']) ? (array_values($budget['income_data']))  : [];
            $budgetTotal = array();
            foreach ($budgetTotalArrs as $budgetTotalArr) {
                foreach ($budgetTotalArr as $k => $value) {
                    $budgetTotal[$k] = (isset($budgetTotal[$k]) ? $budgetTotal[$k] + $value : $value);
                }
            }

            $budget['expense_data'] = json_decode($budget->expense_data, true);
            $budgetExpenseTotalArrs       = !empty($budget['expense_data']) ? (array_values($budget['expense_data']))  : [];

            $budgetExpenseTotal = array();
            foreach ($budgetExpenseTotalArrs as $budgetExpenseTotalArr) {
                foreach ($budgetExpenseTotalArr as $k => $value) {
                    $budgetExpenseTotal[$k] = (isset($budgetExpenseTotal[$k]) ? $budgetExpenseTotal[$k] + $value : $value);
                }
            }

            $data['monthList']      = $month = $this->yearMonth();       //Monthly

            $data['quarterly_monthlist'] =
                [
                    //Quarterly
                    '1-3' => 'Jan-Mar',
                    '4-6' => 'Apr-Jun',
                    '7-9' => 'Jul-Sep',
                    '10-12' => 'Oct-Dec',
                ];

            $data['half_yearly_monthlist'] =
                [
                    // Half - Yearly
                    '1-6' => 'Jan-Jun',
                    '7-12' => 'Jul-Dec',
                ];

            $data['yearly_monthlist'] =
                [
                    // Yearly
                    '1-12' => 'Jan-Dec',
                ];

            $data['yearList'] = $this->yearList();
            if (!empty($budget->from)) {
                $year = $budget->from;
            } else {
                $year = date('Y');
            }
            $data['currentYear'] = $year;

            $income_data = is_string($budget->income_data) ? json_decode($budget->income_data, true) : (is_array($budget->income_data) ? $budget->income_data : []);
            $income_product = array_keys($income_data);
            $incomeproduct = Category::whereIn('id', $income_product)->where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', '=', 1)->get();
            $incomeArr      = [];
            $incomeTotalArr = [];

            foreach ($incomeproduct as $cat) {
                if ($budget->period == 'monthly') {
                    $monthIncomeArr      = [];
                    $monthTotalIncomeArr = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $revenuAmount = Revenue::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $revenuAmount->where('category_id', $cat->id);
                        $revenuAmount->whereRAW('YEAR(date) =?', [$year]);
                        $revenuAmount->whereRAW('MONTH(date) =?', [$i]);
                        $revenuAmount = $revenuAmount->sum('amount');

                        $revenuTotalAmount = Revenue::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $revenuTotalAmount->whereRAW('YEAR(date) =?', [$year]);
                        $revenuTotalAmount->whereRAW('MONTH(date) =?', [$i]);
                        $revenuTotalAmount = $revenuTotalAmount->sum('amount');


                        $invoices = Invoice::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $invoices->where('category_id', $cat->id);
                        $invoices->whereRAW('YEAR(send_date) =?', [$year]);
                        $invoices->whereRAW('MONTH(send_date) =?', [$i]);
                        $invoices      = $invoices->get();
                        $invoiceAmount = 0;
                        foreach ($invoices as $invoice) {
                            $invoiceAmount += $invoice->getTotal();
                        }

                        $invoicesTotal = Invoice::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $invoicesTotal->whereRAW('YEAR(send_date) =?', [$year]);
                        $invoicesTotal->whereRAW('MONTH(send_date) =?', [$i]);
                        $invoicesTotal = $invoicesTotal->get();

                        $invoiceTotalAmount = 0;
                        foreach ($invoicesTotal as $invoiceTotal) {
                            $invoiceTotalAmount += $invoiceTotal->getTotal();
                        }

                        $month = date("F", strtotime(date('Y-' . $i)));

                        $monthIncomeArr[$month] = $invoiceAmount + $revenuAmount;
                        $incomeTotalArr[$month] = $invoiceTotalAmount + $revenuTotalAmount;
                    }
                    $incomeArr[$cat->id] = $monthIncomeArr;
                } else if ($budget->period == 'quarterly' || $budget->period == 'half-yearly' || $budget->period == 'yearly') {

                    if ($budget->period == 'quarterly') {
                        $durations = $data['quarterly_monthlist'];
                    } elseif ($budget->period == 'yearly') {
                        $durations = $data['yearly_monthlist'];
                    } else {
                        $durations = $data['half_yearly_monthlist'];
                    }

                    $monthIncomeArr = [];
                    foreach ($durations as $monthnumber => $monthName) {
                        $month        = explode('-', $monthnumber);
                        $revenuAmount = Revenue::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $revenuAmount->where('category_id', $cat->id);
                        $revenuAmount->whereRAW('YEAR(date) =?', [$year]);
                        $revenuAmount->whereRAW('MONTH(date) >=?', $month[0]);
                        $revenuAmount->whereRAW('MONTH(date) <=?', $month[1]);
                        $revenuAmount = $revenuAmount->sum('amount');

                        $revenuTotalAmount = Revenue::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $revenuTotalAmount->whereRAW('YEAR(date) =?', [$year]);
                        $revenuTotalAmount->whereRAW('MONTH(date) >=?', $month[0]);
                        $revenuTotalAmount->whereRAW('MONTH(date) <=?', $month[1]);
                        $revenuTotalAmount = $revenuTotalAmount->sum('amount');

                        $invoices = Invoice::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $invoices->where('category_id', $cat->id);
                        $invoices->whereRAW('YEAR(send_date) =?', [$year]);
                        $invoices->whereRAW('MONTH(send_date) >=?', $month[0]);
                        $invoices->whereRAW('MONTH(send_date) <=?', $month[1]);
                        $invoices = $invoices->get();

                        $invoiceAmount = 0;
                        foreach ($invoices as $invoice) {
                            $invoiceAmount += $invoice->getTotal();
                        }

                        $invoicesTotal = Invoice::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $invoicesTotal->whereRAW('YEAR(send_date) =?', [$year]);
                        $invoicesTotal->whereRAW('MONTH(send_date) >=?', $month[0]);
                        $invoicesTotal->whereRAW('MONTH(send_date) <=?', $month[1]);
                        $invoicesTotal = $invoicesTotal->get();

                        $invoiceTotalAmount = 0;
                        foreach ($invoicesTotal as $invoiceTotal) {
                            $invoiceTotalAmount += $invoiceTotal->getTotal();
                        }

                        $monthIncomeArr[$monthName] = $invoiceAmount + $revenuAmount;
                        $incomeTotalArr[$monthName] = $invoiceTotalAmount + $revenuTotalAmount;
                    }
                    $incomeArr[$cat->id] = $monthIncomeArr;
                }
            }
            $expense_data = is_string($budget->expense_data) ? json_decode($budget->expense_data, true) : (is_array($budget->expense_data) ? $budget->expense_data : []);
            $expense_product = array_keys($expense_data);
            $expenseproduct = Category::whereIn('id', $expense_product)->where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', '=', 2)->get();

            $expenseArr = [];
            $expenseTotalArr = [];

            foreach ($expenseproduct as $expense) {
                if ($budget->period == 'monthly') {
                    $monthExpenseArr = [];
                    $monthTotalExpenseArr = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $paymentAmount = Payment::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $paymentAmount->where('category_id', $expense->id);
                        $paymentAmount->whereRAW('YEAR(date) =?', [$year]);
                        $paymentAmount->whereRAW('MONTH(date) =?', [$i]);
                        $paymentAmount = $paymentAmount->sum('amount');

                        $paymentTotalAmount = Payment::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $paymentTotalAmount->whereRAW('YEAR(date) =?', [$year]);
                        $paymentTotalAmount->whereRAW('MONTH(date) =?', [$i]);
                        $paymentTotalAmount = $paymentTotalAmount->sum('amount');


                        $bills = Bill::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $bills->where('category_id', $expense->id);
                        $bills->whereRAW('YEAR(send_date) =?', [$year]);
                        $bills->whereRAW('MONTH(send_date) =?', [$i]);
                        $bills = $bills->get();

                        $billAmount = 0;
                        foreach ($bills as $bill) {
                            $billAmount += $bill->getTotal();
                        }

                        $billsTotal = Bill::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $billsTotal->whereRAW('YEAR(send_date) =?', [$year]);
                        $billsTotal->whereRAW('MONTH(send_date) =?', [$i]);
                        $billsTotal = $billsTotal->get();

                        $billTotalAmount = 0;
                        foreach ($billsTotal as $billTotal) {
                            $billTotalAmount += $billTotal->getTotal();
                        }

                        $month                   = date("F", strtotime(date('Y-' . $i)));
                        $monthExpenseArr[$month] = $billAmount + $paymentAmount;
                        $expenseTotalArr[$month] = $billTotalAmount + $paymentTotalAmount;
                    }
                    $expenseArr[$expense->id] = $monthExpenseArr;
                } else if ($budget->period == 'quarterly' || $budget->period == 'half-yearly' || $budget->period == 'yearly') {
                    if ($budget->period == 'quarterly') {
                        $durations = $data['quarterly_monthlist'];
                    } elseif ($budget->period == 'yearly') {
                        $durations = $data['yearly_monthlist'];
                    } else {
                        $durations = $data['half_yearly_monthlist'];
                    }

                    $monthExpenseArr = [];
                    foreach ($durations as $monthnumber => $monthName) {
                        $month         = explode('-', $monthnumber);
                        $paymentAmount = Payment::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $paymentAmount->where('category_id', $expense->id);
                        $paymentAmount->whereRAW('YEAR(date) =?', [$year]);
                        $paymentAmount->whereRAW('MONTH(date) >=?', $month[0]);
                        $paymentAmount->whereRAW('MONTH(date) <=?', $month[1]);
                        $paymentAmount = $paymentAmount->sum('amount');


                        $month         = explode('-', $monthnumber);
                        $paymentTotalAmount = Payment::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $paymentTotalAmount->whereRAW('YEAR(date) =?', [$year]);
                        $paymentTotalAmount->whereRAW('MONTH(date) >=?', $month[0]);
                        $paymentTotalAmount->whereRAW('MONTH(date) <=?', $month[1]);
                        $paymentTotalAmount = $paymentTotalAmount->sum('amount');

                        $bills = Bill::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $bills->where('category_id', $expense->id);
                        $bills->whereRAW('YEAR(send_date) =?', [$year]);
                        $bills->whereRAW('MONTH(send_date) >=?', $month[0]);
                        $bills->whereRAW('MONTH(send_date) <=?', $month[1]);
                        $bills = $bills->get();

                        $billAmount = 0;
                        foreach ($bills as $bill) {
                            $billAmount += $bill->getTotal();
                        }

                        $billsTotal = Bill::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                        $billsTotal->whereRAW('YEAR(send_date) =?', [$year]);
                        $billsTotal->whereRAW('MONTH(send_date) >=?', $month[0]);
                        $billsTotal->whereRAW('MONTH(send_date) <=?', $month[1]);
                        $billsTotal = $billsTotal->get();

                        $BillTotalAmount = 0;
                        foreach ($billsTotal as $billTotal) {
                            $BillTotalAmount += $billTotal->getTotal();
                        }
                        $monthExpenseArr[$monthName] = $billAmount + $paymentAmount;
                        $expenseTotalArr[$monthName] = $BillTotalAmount + $paymentTotalAmount;
                    }
                    $expenseArr[$expense->id] = $monthExpenseArr;
                }
                // NET PROFIT OF BUDGET
                $budgetprofit = [];
                $keys   = array_keys($budgetTotal + $budgetExpenseTotal);
                foreach ($keys as $v) {
                    $budgetprofit[$v] = (empty($budgetTotal[$v]) ? 0 : $budgetTotal[$v]) - (empty($budgetExpenseTotal[$v]) ? 0 : $budgetExpenseTotal[$v]);
                }
                $data['budgetprofit']              = $budgetprofit;

                // NET PROFIT OF ACTUAL
                $actualprofit = [];
                $keys   = array_keys($incomeTotalArr + $expenseTotalArr);
                foreach ($keys as $v) {
                    $actualprofit[$v] = (empty($incomeTotalArr[$v]) ? 0 : $incomeTotalArr[$v]) - (empty($expenseTotalArr[$v]) ? 0 : $expenseTotalArr[$v]);
                }
                $data['actualprofit']              = $actualprofit;
            }

            return view('budget-planner::budget.show', compact(
                'id',
                'budget',
                'incomeproduct',
                'expenseproduct',
                'incomeArr',
                'expenseArr',
                'incomeTotalArr',
                'expenseTotalArr',
                'budgetTotal',
                'budgetExpenseTotal'
            ), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('budget plan edit')) {
            try {
                $id       = Crypt::decrypt($id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Budget Not Found.'));
            }
            $budget = Budget::find($id);

            $budget['income_data']  = json_decode($budget->income_data, true);
            $budget['expense_data'] = json_decode($budget->expense_data, true);

            $periods = Budget::$period;

            $data['monthList'] = $month = $this->yearMonth();        //Monthly

            $data['quarterly_monthlist'] =
                [
                    //Quarterly
                    'Jan-Mar',
                    'Apr-Jun',
                    'Jul-Sep',
                    'Oct-Dec',
                ];

            $data['half_yearly_monthlist'] =
                [
                    // Half - Yearly
                    'Jan-Jun',
                    'Jul-Dec',
                ];

            $data['yearly_monthlist'] =
                [
                    // Yearly
                    'Jan-Dec',
                ];

            $data['yearList'] = $this->yearList();

            $incomeproduct  = Category::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', '=', 1)->get();
            $expenseproduct = Category::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', '=', 2)->get();


            return view('budget-planner::budget.edit', compact('periods', 'budget', 'incomeproduct', 'expenseproduct'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('budget plan edit')) {
            $budget = Budget::find($id);
            if ($budget->created_by == creatorId() && $budget->workspace == getActiveWorkSpace()) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required|string|max:120',
                    'period' => 'required',

                ]);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $budget->name         = $request->name;
                $budget->from         = $request->year;
                $budget->period       = $request->period;
                $budget->income_data  = json_encode($request->income);
                $budget->expense_data = json_encode($request->expense);
                $budget->save();

                event(new UpdateBudgetPlan($request, $budget));

                return redirect()->route('budget.index')->with('success', __(' The budget plan are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (\Auth::user()->isAbleTo('budget plan delete')) {
            $budget = Budget::find($id);
            if ($budget->created_by == creatorId()) {
                event(new DestroyBudgetPlan($budget));

                $budget->delete();

                return redirect()->route('budget.index')->with('success', __('The budget plan are deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
