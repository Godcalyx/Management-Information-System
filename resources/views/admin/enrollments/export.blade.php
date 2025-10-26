<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>LSHS Enrollment - {{ $enrollment->first_name }} {{ $enrollment->last_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .text-center { text-align: center; }

        .header {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0px 0px;
    border-bottom: 2px solid #000;
    margin-bottom: 20px;
    margin-top: -140px;
}

.logo {
    width: 190px;
    height: auto;
    margin-left: 80px;
    position: relative;
    top: 130px;    
}

.header-text {
    text-align: center;
    max-width: 400px;
    margin: 0 auto;
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

        .logo {
            width: 230px; /* or adjust as needed */
            flex-shrink: 0;
        }
        .header-text h4,
        .header-text h3, 
        .header-text h2, 
        .header-text p {
            margin-left: 110px;
            line-height: 1.2;

        }
        .section-title {
            background-color: #e2f0d9; 
            font-weight: bold; 
            padding: 5px; 
            margin-top: 15px;
        }
        table {
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px;
        }
        td, th {
            border: 1px solid #000; 
            padding: 5px; 
            vertical-align: top;
        }
        ul { padding-left: 20px; }
    </style>
</head>
<body>

    {{-- Header --}}
<div class="header">
    <img src="{{ public_path('images/logo.jpg') }}" class="logo" alt="CvSU Logo" />
    <div class="header-text">
        <h4 style="font-weight: bold; margin-top: 2px;">Republic of the Philippines</h4>
        <h2 style="font-weight: bold; margin-top: 2px;">CAVITE STATE UNIVERSITY NAIC</h2>
        <p style="font-size: 11px; font-style: italic; margin-top: 1px;">(Formerly CAVITE COLLEGE OF FISHERIES)</p>
        <p style="margin-top: 5px;">Bucana Malaki, Naic, Cavite</p>
        <p style="margin-top: 2px;"><a href="https://www.cvsu-naic.edu.ph" style="color: #000; text-decoration: none;">www.cvsu-naic.edu.ph</a></p>
    </div>
</div>


    <h3 class="text-center">LEARNER ENROLLMENT FORM</h3>
    <p class="text-center"><em>Adapted from DepEd Enhanced Basic Education Enrollment Form</em></p>

    {{-- Enrollment Info --}}
    <div class="section-title">A. ENROLLMENT INFORMATION</div>
    <table>
        <tr>
            <td><strong>School Year:</strong> {{ $enrollment->school_year }}</td>
            <td><strong>Grade Level:</strong> {{ $enrollment->grade_level }}</td>
        </tr>
        <tr>
            <td><strong>LRN:</strong> {{ $enrollment->lrn }}</td>
            <td><strong>With LRN?:</strong> {{ $enrollment->lrn ? 'Yes' : 'No' }}</td>
        </tr>
    </table>

    {{-- Learner Info --}}
    <div class="section-title">B. LEARNER INFORMATION</div>
    <table>
        <tr>
            <td><strong>Last Name:</strong> {{ $enrollment->last_name }}</td>
            <td><strong>First Name:</strong> {{ $enrollment->first_name }}</td>
            <td><strong>Middle Name:</strong> {{ $enrollment->middle_name }}</td>
            <td><strong>Extension:</strong> {{ $enrollment->extension_name }}</td>
        </tr>
        <tr>
            <td><strong>Birthdate:</strong> {{ $enrollment->birthdate }}</td>
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

    {{-- Address Info --}}
    <div class="section-title">C. ADDRESS INFORMATION</div>
    <table>
        <tr>
            <th colspan="2">Current Address</th>
            <th colspan="2">Permanent Address</th>
        </tr>
        <tr>
            <td><strong>House #</strong></td><td>{{ $enrollment->current_house }}</td>
            <td><strong>House #</strong></td><td>{{ $enrollment->permanent_house }}</td>
        </tr>
        <tr>
            <td><strong>Street</strong></td><td>{{ $enrollment->current_street }}</td>
            <td><strong>Street</strong></td><td>{{ $enrollment->permanent_street }}</td>
        </tr>
        <tr>
            <td><strong>Barangay</strong></td><td>{{ $enrollment->current_barangay }}</td>
            <td><strong>Barangay</strong></td><td>{{ $enrollment->permanent_barangay }}</td>
        </tr>
        <tr>
            <td><strong>City</strong></td><td>{{ $enrollment->current_city }}</td>
            <td><strong>City</strong></td><td>{{ $enrollment->permanent_city }}</td>
        </tr>
        <tr>
            <td><strong>Province</strong></td><td>{{ $enrollment->current_province }}</td>
            <td><strong>Province</strong></td><td>{{ $enrollment->permanent_province }}</td>
        </tr>
        <tr>
            <td><strong>Zip Code</strong></td><td>{{ $enrollment->current_zip }}</td>
            <td><strong>Zip Code</strong></td><td>{{ $enrollment->permanent_zip }}</td>
        </tr>
        <tr>
            <td><strong>Country</strong></td><td>{{ $enrollment->current_country }}</td>
            <td><strong>Country</strong></td><td>{{ $enrollment->permanent_country }}</td>
        </tr>
    </table>

    {{-- Parent Info --}}
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

    <br>
    <p style="text-indent: 40px;">
    <small>
        I hereby certify that the above information given are true and correct to the best of my knowledge and I allow the Cavite State University Naic
        to use my child’s details to create and/or update his/her learner profile in the Learner Information System. The information herein shall be treated as 
        confidential in compliance with the Data Privacy Act of 2012.
    </small>
</p>

    <br>
    <br>


    <p style="border-top: 1px solid black; width: 300px; margin: 5px 0 2px 0;"></p>
<p>Signature over Printed Name of Parent/Guardian</p>

    <br>

    <p style="border-top: 1px solid black; width: 150px; margin: 10px 0 2px 0;"></p>
<p>Date</p>


</body>
</html>
