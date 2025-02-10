<html>

<head>
    <style>
        table {
            border: 1px dashed #000;
        }
    </style>
</head>

<body>
    <table>
        <tbody>
            <tr>
                <th>
                    {{ env('APP_NAME') }} LEAVE REPORT
                </th>
            </tr>
            <tr style="background-color: #ffb53a; color: #FFFFFF;">
                <th>No</th>
                <th>Date Applied</th>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Status</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Notes</th>
            </tr>

            @foreach ($leaves as $leave)
                <tr>
                    <td> {{ $loop->iteration }}</td>
                    <td> {{ $leave->created_at }}</td>
                    <td> {{ $leave->fullName }}</td>
                    <td> {{ $leave->leaveType }}</td>
                    <td> {{ $leave->status }}</td>
                    <td> {{ $leave->from }}</td>
                    <td> {{ $leave->to }}</td>
                    <td> {{ $leave->days }}</td>
                    <td> {{ $leave->notes }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
