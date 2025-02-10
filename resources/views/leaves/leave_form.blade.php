<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            text-align: left;
            background-color: #f2f2f2;
        }

        img {
            width: 200px;
            height: 90px;
        }

        h1,
        h3 {
            text-align: center;
        }

        hr {
            border: none;
            border-top: 2px solid #ddd;
            margin: 20px 0;
        }

        .company-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td>
                <img src="public/assets/img/logo.png" alt="Boxleo Logo" style="width: 200px; height: 90px;">
            </td>
            <td>Boxleo Courier & Fulfilment Limited <br>
                Akshrap Godowns, Gate A-2, JKIA Junction <br>
                Mombasa Road.
            </td>
            <td>Telephone: +254711082433 <br>
                Email: info@boxleocourier.com <br>
                Website: www.boxleocourier.com
            </td>
        </tr>
    </table>
    <hr>

    <h1>Leave Application Form</h1>
    <table>
        <tr>
            <th>Employee Name</th>
            <td>{{ $leaveData['user']['firstname'] }} {{ $leaveData['user']['lastname'] }}</td>
        </tr>
        <tr>
            <th>Department</th>
            <td>{{ $user->department->name }}</td>
        </tr>
        <tr>
            <th>Date to Commence Leave</th>
            <td>{{ $leaveData['from'] }}</td>
        </tr>
        <tr>
            <th>Last Day of Leave</th>
            <td>{{ $leaveData['to'] }}</td>
        </tr>
        <tr>
            <th>Leave Days Taken</th>
            <td>{{ $leaveData['days'] }}</td>
        </tr>
        <tr>
            <th>Leave Type</th>
            <td>{{ str_replace('_', ' ', $leaveData['leave_type']['name']) }}</td>
        </tr>
    </table>

    <h3>Employee Details</h3>
    <table>
        <tr>
            <th>Annual Leave Balance Before This Application</th>
            <td> </td>
        </tr>
        <tr>
            <th>Employee Phone Number</th>
            <td>{{ $leaveData['user']['phone'] }}</td>
        </tr>
        <tr>
            <th>Date of Leave Application</th>
            <td>{{ $leaveData['created_at'] }}</td>
        </tr>
    </table>

    <h3>For HR Department Use Only</h3>
    <table>
        <tr>
            <th>Previous Leave Balance</th>
            <td> </td>
        </tr>
        <tr>
            <th>Less Applied Leave</th>
            <td> </td>
        </tr>
        <tr>
            <th>Leave Balance to Date</th>
            <td> </td>
        </tr>
        <tr>
            <th>Comments</th>
            <td>{{ $leaveData['comment'] }}</td>
        </tr>
    </table>
</body>

</html>
