<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $employee->firstname . ' ' . $employee->lastname }} - Payslip</title>
    <style>
        .container {
            text-align: center;
        }

        .company {
            text-align: left;
        }

        .address {
            text-align: left;
        }

        .personal-details {
            text-align: left;
            margin-top: 20px;
        }

        .personal-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .personal-details th,
        .personal-details td {
            border: .5px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .salary-details {
            text-align: left;
            margin-top: 20px;
        }

        .salary-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .salary-details th,
        .salary-details td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
        }

        .earnings-deductions-table {
            display: flex;
        }

        .earnings-table,
        .deductions-table {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <tr>
                <td>
                    <div class="company">
                        <h4>
                            Boxleo Courier & Fulfillment Services Ltd <br>
                            Akshrap Godowns, Gate A-2, JKIA Junction <br>
                            Mombasa Road <br>
                        </h4>
                    </div>
                </td>
                <td>
                    <div class="address">
                        <h6>Telephone: +254 711 082 433 <br>
                            Email: info@boxleocourier.com <br>
                            Website: www.boxleocourier.com</h6>
                    </div>
                </td>
                <td>
                    <img src="assets/img/logo.png" alt="" style="width: 200px; height: 80px;">
                </td>
            </tr>
        </table>
        <div>
            <h4 style="text-transform: uppercase; text-align: left;">{{ $employee->firstname }} {{ $employee->lastname }}</h4>
        </div>
        <hr>
        <div class="personal-details">
            <table>
                <tr>
                    <td><b>Job Number</b> <br> {{ $employee->id }}</td>
                    <td><b>Date Joined</b> <br> {{ $employee->employee_job_info->employment_date ?? 'N/A' }}</td>
                    <td><b>Office</b> <br> {{ $employee->office->name }}</td>
                    <td><b>Department</b> <br> {{ $employee->department->name }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td><b>Department</b> <br> {{ $employee->department->name }}</td>
                    <td><b>Designation</b> <br> {{ $employee->designation->name }}</td>
                    <td><b>Date Joined</b> <br> {{ $employee->employee_job_info->employment_date ?? 'N/A' }}</td>
                    <td><b>Payment Mode</b> <br> {{ $employee->employee_detail->payment_mode }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td><b>Bank Name</b> <br> {{ $employee->employee_detail->bank_name }}</td>
                    <td><b>Bank Branch</b> <br> {{ $employee->employee_detail->bank_branch }}</td>
                    <td><b>Bank Account</b> <br> {{ $employee->employee_detail->bank_account }}</td>
                </tr>
            </table>
        </div>
        <div class="salary-details">
            <h4 style="text-transform: uppercase; text-align: left;">SALARY DETAILS</h4>
            <div class="earnings-deductions-table">
                <div class="earnings-table">
                    <h4>EARNINGS</h4>
                    <table>
                        <tr>
                            <td><b>Basic Pay:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                            <td><b>House Allowance:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                            <td><b>Transport Allowance:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                            <td><b>Total Earnings:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                        </tr>
                    </table>
                </div>
                <div class="deductions-table">
                    <h4>DEDUCTIONS</h4>
                    <table>
                        <tr>
                            <td><b>Income Tax:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                            <td><b>NHIF:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                            <td><b>NSSF:</b> <br>{{ $employee->employee_salary->income_tax}}</td>
                            <td><b>Tax Relief:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                            <td><b>PAYE:</b> <br> {{ $employee->employee_salary->income_tax}}</td>
                        </tr>
                    </table>
                </div>
                <div class="salary-summary">
                    <h3><b>Net salary payable:</b> {{$employee->employee_salary->net_salary}}</h3>
                    <p>Net salary in words: {{$employee->employee_salary->net_salary}}</p>

                </div>

            </div>
        </div>
    </div>
</body>

</html>
