<?php

declare(strict_types=1);

namespace Enlivy\Enums\Payslip;

use Enlivy\Enums\Concern\EnumValues;

enum LineCodes: string
{
    use EnumValues;

    case NORM_HOURS = 'norm_hours';
    case WORKED_HOURS = 'worked_hours';
    case WORKED_DAYS = 'worked_days';
    case OVERTIME_HOURS_1 = 'overtime_hours_1';
    case OVERTIME_HOURS_2 = 'overtime_hours_2';
    case NIGHT_HOURS = 'night_hours';
    case WEEKEND_HOURS = 'weekend_hours';
    case LEAVE_DAYS = 'leave_days';
    case SICK_DAYS = 'sick_days';
    case UNPAID_LEAVE_DAYS = 'unpaid_leave_days';
    case UNEXCUSED_DAYS = 'unexcused_days';
    case CHILDCARE_DAYS = 'childcare_days';
    case OTHER_INFORMATIONAL = 'other_informational';
    case BASE_SALARY = 'base_salary';
    case EARNED_SALARY = 'earned_salary';
    case BONUS = 'bonus';
    case COMMISSION = 'commission';
    case OVERTIME_PREMIUM_1 = 'overtime_premium_1';
    case OVERTIME_PREMIUM_2 = 'overtime_premium_2';
    case NIGHT_PREMIUM = 'night_premium';
    case WEEKEND_PREMIUM = 'weekend_premium';
    case LEAVE_PAY = 'leave_pay';
    case SICK_PAY = 'sick_pay';
    case STATUTORY_PAY = 'statutory_pay';
    case MEAL_VOUCHERS = 'meal_vouchers';
    case EXPENSE_REIMBURSEMENT = 'expense_reimbursement';
    case OTHER_EARNING = 'other_earning';
    case SOCIAL_SECURITY_EMPLOYEE = 'social_security_employee';
    case HEALTH_INSURANCE_EMPLOYEE = 'health_insurance_employee';
    case PENSION_EMPLOYEE = 'pension_employee';
    case UNEMPLOYMENT_INSURANCE_EMPLOYEE = 'unemployment_insurance_employee';
    case OTHER_EMPLOYEE_CONTRIBUTION = 'other_employee_contribution';
    case PERSONAL_ALLOWANCE = 'personal_allowance';
    case OTHER_TAX_RELIEF = 'other_tax_relief';
    case INCOME_TAX = 'income_tax';
    case STATE_INCOME_TAX = 'state_income_tax';
    case LOCAL_INCOME_TAX = 'local_income_tax';
    case OTHER_TAX = 'other_tax';
    case ADVANCE = 'advance';
    case GARNISHMENT = 'garnishment';
    case LOAN_REPAYMENT = 'loan_repayment';
    case UNION_DUES = 'union_dues';
    case OTHER_POST_TAX_DEDUCTION = 'other_post_tax_deduction';
    case SOCIAL_SECURITY_EMPLOYER = 'social_security_employer';
    case HEALTH_INSURANCE_EMPLOYER = 'health_insurance_employer';
    case PENSION_EMPLOYER = 'pension_employer';
    case UNEMPLOYMENT_INSURANCE_EMPLOYER = 'unemployment_insurance_employer';
    case OTHER_EMPLOYER_CONTRIBUTION = 'other_employer_contribution';
}
