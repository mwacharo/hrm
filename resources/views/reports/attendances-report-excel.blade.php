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
                    {{ env('APP_NAME') }} ATTENDANCE REPORT
                </th>
            </tr>
            <tr style="background-color: #ffb53a; color: #FFFFFF;">
                <th>No</th>
                <th>Employee</th>
                <th>Attendance Date</th>
                <th>Status</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>

            @foreach ($attendances as $attendance)
                <tr>
                    <td> {{ $loop->iteration }}</td>
                    <td> {{ $attendance->name }} </td>
                    <td> {{ $attendance->attendance_date }}</td>
                    <td> {{ $attendance->status }}</td>
                    <td> {{ $attendance->clock_in }}</td>
                    <td> {{ $attendance->clock_out }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
