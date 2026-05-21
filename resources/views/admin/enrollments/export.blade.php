<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LSHS Enrollment - {{ $enrollment->first_name }} {{ $enrollment->last_name }}</title>
    
    <style>
        html, body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .text-center { text-align: center; }
        h2, h3, h4 { margin: 2px 0; }
        h2, h3 { font-family: Helvetica, Arial, sans-serif; font-weight: bold; }
        p { margin: 2px 0; }

        /* Header */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 18px;
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border: none;
            margin: 0;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 90px;
        }

        .logo {
            width: 78px;
            height: auto;
            display: block;
        }

        .header-text {
            text-align: center;
            padding-left: 10px;
        }

        .header-text h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 2px 0;
        }

        .header-text h4 {
            font-size: 13px;
            margin: 2px 0;
        }

        .header-text p {
            font-size: 11px;
            margin: 2px 0;
        }

        .header-text h4,
        .header-text h3, 
        .header-text h2, 
        .header-text p {
            line-height: 1.2;
        }

        /* Sections */
        .section-title {
            background-color: #e2f0d9;
            font-weight: bold;
            padding: 4px 6px;
            margin-top: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td, th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 11px;
        }

        th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        /* Signature */
        .signature-line {
            border-top: 1px solid black;
            width: 250px;
            margin-top: 25px;
        }

        .signature-date {
            border-top: 1px solid black;
            width: 150px;
            margin-top: 10px;
        }

        small { font-size: 10px; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo.jpg') }}" class="logo" alt="CvSU Logo">
            </td>
            <td>
                <div class="header-text">
                    <h4>Republic of the Philippines</h4>
                    <h2>CAVITE STATE UNIVERSITY NAIC</h2>
                    <p style="font-style: italic;">(Formerly CAVITE COLLEGE OF FISHERIES)</p>
                    <p>Bucana Malaki, Naic, Cavite</p>
                    <p>www.cvsu-naic.edu.ph</p>
                </div>
            </td>
        </tr>
    </table>
</div>

<h3 class="text-center">LEARNER ENROLLMENT FORM</h3>
<p class="text-center"><em>Adapted from DepEd Enhanced Basic Education Enrollment Form</em></p>

<!-- Enrollment Info -->
<div class="section-title">A. ENROLLMENT INFORMATION</div>
<table>
    <tr>
        <td><strong>School Year:</strong> {{ $enrollment->school_year }}</td>
        <td><strong>Grade Level:</strong> {{ $enrollment->gradeLevel->name ?? $enrollment->grade_level_id ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>LRN:</strong> {{ $enrollment->lrn }}</td>
        <td><strong>With LRN?:</strong> {{ $enrollment->lrn ? 'Yes' : 'No' }}</td>
    </tr>
</table>

<!-- Learner Info -->
<div class="section-title">B. LEARNER INFORMATION</div>
<table>
    <tr>
        <td><strong>Last Name:</strong> {{ $enrollment->last_name }}</td>
        <td><strong>First Name:</strong> {{ $enrollment->first_name }}</td>
        <td><strong>Middle Name:</strong> {{ $enrollment->middle_name }}</td>
        <td><strong>Extension:</strong> {{ $enrollment->extension_name }}</td>
    </tr>
    <tr>
        <td><strong>Birthdate:</strong> {{ optional($enrollment->birthdate)->format('F d, Y') }}</td>
        <td><strong>Place of Birth:</strong> {{ $enrollment->birthplace }}</td>
        <td><strong>Sex:</strong> {{ $enrollment->sex }}</td>
        <td><strong>Mother Tongue:</strong> {{ $enrollment->mother_tongue }}</td>
    </tr>
    <tr>
        <td><strong>Age:</strong> {{ \Carbon\Carbon::parse($enrollment->birthdate)->age }}</td>
        <td><strong>IP Community?:</strong> {{ $enrollment->ip_community }}</td>
        <td colspan="2"><strong>Specify:</strong> {{ $enrollment->ip_specify }}</td>
    </tr>
    <tr>
        <td><strong>4Ps Beneficiary?:</strong> {{ $enrollment->is_4ps }}</td>
        <td colspan="3"><strong>Household ID:</strong> {{ $enrollment->household_id }}</td>
    </tr>
</table>

<!-- Address Info -->
<div class="section-title">C. ADDRESS INFORMATION</div>
<table>
    <tr>
        <th colspan="2">Current Address</th>
        <th colspan="2">Permanent Address</th>
    </tr>
    <tr>
        <td>House #</td><td>{{ $enrollment->current_house }}</td>
        <td>House #</td><td>{{ $enrollment->permanent_house }}</td>
    </tr>
    <tr>
        <td>Street</td><td>{{ $enrollment->current_street }}</td>
        <td>Street</td><td>{{ $enrollment->permanent_street }}</td>
    </tr>
    <tr>
        <td>Barangay</td><td>{{ $enrollment->current_barangay }}</td>
        <td>Barangay</td><td>{{ $enrollment->permanent_barangay }}</td>
    </tr>
    <tr>
        <td>City</td><td>{{ $enrollment->current_city }}</td>
        <td>City</td><td>{{ $enrollment->permanent_city }}</td>
    </tr>
    <tr>
        <td>Province</td><td>{{ $enrollment->current_province }}</td>
        <td>Province</td><td>{{ $enrollment->permanent_province }}</td>
    </tr>
    <tr>
        <td>Zip Code</td><td>{{ $enrollment->current_zip }}</td>
        <td>Zip Code</td><td>{{ $enrollment->permanent_zip }}</td>
    </tr>
    <tr>
        <td>Country</td><td>{{ $enrollment->current_country }}</td>
        <td>Country</td><td>{{ $enrollment->permanent_country }}</td>
    </tr>
</table>

<!-- Parent Info -->
<div class="section-title">D. PARENT / GUARDIAN INFORMATION</div>
<table>
    <tr>
        <th>Father's Name</th>
        <td>{{ $enrollment->father_last }}, {{ $enrollment->father_first }} {{ $enrollment->father_middle }}</td>
        <th>Contact</th>
        <td>{{ $enrollment->father_contact }}</td>
    </tr>
    <tr>
        <th>Mother's Name</th>
        <td>{{ $enrollment->mother_last }}, {{ $enrollment->mother_first }} {{ $enrollment->mother_middle }}</td>
        <th>Contact</th>
        <td>{{ $enrollment->mother_contact }}</td>
    </tr>
    <tr>
        <th>Guardian's Name</th>
        <td>{{ $enrollment->guardian_last }}, {{ $enrollment->guardian_first }} {{ $enrollment->guardian_middle }}</td>
        <th>Contact</th>
        <td>{{ $enrollment->guardian_contact }}</td>
    </tr>
</table>

<p style="text-indent: 40px;"><small>
I hereby certify that the above information given are true and correct to the best of my knowledge and I allow the Cavite State University Naic
to use my child’s details to create and/or update his/her learner profile in the Learner Information System. The information herein shall be treated as 
confidential in compliance with the Data Privacy Act of 2012.
</small></p>

<!-- Signature -->
<div class="signature">
    <div class="signature-line"></div>
    <p>Signature over Printed Name of Parent/Guardian</p>

    <div class="signature-date"></div>
    <p>Date</p>
</div>

</body>
</html>
