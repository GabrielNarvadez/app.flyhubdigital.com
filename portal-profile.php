<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Customer Portal Profile | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/portal-menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                </div>
                                <h4 class="page-title">Customer Portal Profile</h4>
                                
                                    <div class="container py-4">
                                        <!-- Profile Header -->
                                        <div class="row mb-4">
                                            <div class="col-md-12 d-flex align-items-center gap-3">
                                                <div>
                                                    <div class="rounded-circle bg-light text-secondary d-flex justify-content-center align-items-center" style="width:64px; height:64px; font-size:2rem;">
                                                        <i class="ri-user-3-line"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h3 class="mb-0" id="profileFullName">Del Rosario, Ana M.</h3>
                                                    <div class="text-muted" id="profileEmail">ana.rosario@email.com</div>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-outline-primary" id="btn-edit-profile"><i class="ri-edit-2-line"></i> Edit Profile</button>
                                                    <button class="btn btn-success d-none" id="btn-save-profile"><i class="ri-save-2-line"></i> Save</button>
                                                    <button class="btn btn-outline-secondary d-none" id="btn-cancel-edit"><i class="ri-close-line"></i> Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Profile Sections -->
                                        <div class="row g-4">
                                            <div class="col-lg-6">
                                                <!-- A. Client’s Information -->
                                                <div class="card shadow-sm mb-4">
                                                    <div class="card-header bg-white fw-bold">Personal Information</div>
                                                    <div class="card-body">
                                                        <form id="form-personal">
                                                            <div class="mb-2">
                                                                <label class="form-label">Client's Name (Last, First, Middle)</label>
                                                                <input type="text" class="form-control" id="clientName" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Contact No.</label>
                                                                <input type="text" class="form-control" id="clientContact" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Email Address</label>
                                                                <input type="email" class="form-control" id="clientEmail" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Civil Status</label>
                                                                    <select class="form-select" id="civilStatus" disabled>
                                                                        <option>Single</option>
                                                                        <option>Married</option>
                                                                        <option>Widowed</option>
                                                                        <option>Divorced</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Date of Birth</label>
                                                                    <input type="date" class="form-control" id="dob" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Place of Birth</label>
                                                                    <input type="text" class="form-control" id="pob" disabled>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Age</label>
                                                                    <input type="number" class="form-control" id="age" disabled>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Nationality</label>
                                                                    <input type="text" class="form-control" id="nationality" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Religion</label>
                                                                <input type="text" class="form-control" id="religion" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Permanent Address</label>
                                                                <input type="text" class="form-control" id="permAddress" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Provincial Address</label>
                                                                <input type="text" class="form-control" id="provAddress" disabled>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- Spouse Info (conditional) -->
                                                <div class="card shadow-sm mb-4" id="spouseCard" style="display:none">
                                                    <div class="card-header bg-white fw-bold">Spouse Information</div>
                                                    <div class="card-body">
                                                        <form id="form-spouse">
                                                            <div class="mb-2">
                                                                <label class="form-label">Spouse's Name (Last, First, Middle)</label>
                                                                <input type="text" class="form-control" id="spouseName" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Spouse's Contact No.</label>
                                                                <input type="text" class="form-control" id="spouseContact" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Spouse's Email</label>
                                                                <input type="email" class="form-control" id="spouseEmail" disabled>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- B. Employment Data -->
                                                <div class="card shadow-sm mb-4">
                                                    <div class="card-header bg-white fw-bold">Employment Data</div>
                                                    <div class="card-body">
                                                        <form id="form-employment">
                                                            <div class="mb-2">
                                                                <label class="form-label">Company Name</label>
                                                                <input type="text" class="form-control" id="companyName" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Company Address</label>
                                                                <input type="text" class="form-control" id="companyAddress" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Length of Employment</label>
                                                                    <input type="text" class="form-control" id="lengthEmployment" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Position</label>
                                                                    <input type="text" class="form-control" id="position" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Company Contact Person</label>
                                                                <input type="text" class="form-control" id="companyContactPerson" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Contact Person Position</label>
                                                                    <input type="text" class="form-control" id="contactPersonPosition" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Contact No.</label>
                                                                    <input type="text" class="form-control" id="companyContactNo" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" class="form-control" id="companyEmail" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">SSS/UMID No.</label>
                                                                    <input type="text" class="form-control" id="sssNo" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">TIN</label>
                                                                    <input type="text" class="form-control" id="tinNo" disabled>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Right Side -->
                                            <div class="col-lg-6">
                                                <!-- C. Property Data -->
                                                <div class="card shadow-sm mb-4">
                                                    <div class="card-header bg-white fw-bold">Property Data</div>
                                                    <div class="card-body">
                                                        <form id="form-property">
                                                            <div class="mb-2">
                                                                <label class="form-label">Project Title</label>
                                                                <input type="text" class="form-control" id="projectTitle" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Project Site</label>
                                                                <input type="text" class="form-control" id="projectSite" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Phase No.</label>
                                                                    <input type="text" class="form-control" id="phaseNo" disabled>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Block No.</label>
                                                                    <input type="text" class="form-control" id="blockNo" disabled>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Lot No.</label>
                                                                    <input type="text" class="form-control" id="lotNo" disabled>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Lot Area</label>
                                                                    <input type="text" class="form-control" id="lotArea" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Lot Class</label>
                                                                    <input type="text" class="form-control" id="lotClass" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Price per sqm</label>
                                                                    <input type="text" class="form-control" id="pricePerSqm" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Agent / Contact No.</label>
                                                                <input type="text" class="form-control" id="agentContact" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Manager</label>
                                                                    <input type="text" class="form-control" id="manager" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Broker</label>
                                                                    <input type="text" class="form-control" id="broker" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Reservation Amount</label>
                                                                <input type="text" class="form-control" id="reservationAmount" disabled>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- D. SOA / Amortization Schedule -->
                                                <div class="card shadow-sm mb-4">
                                                    <div class="card-header bg-white fw-bold">Amortization Schedule Summary</div>
                                                    <div class="card-body">
                                                        <form id="form-amort">
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Client’s Name</label>
                                                                    <input type="text" class="form-control" id="amortClientName" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Date of Reservation</label>
                                                                    <input type="date" class="form-control" id="reservationDate" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Project Title</label>
                                                                <input type="text" class="form-control" id="amortProjectTitle" disabled>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Project Site</label>
                                                                <input type="text" class="form-control" id="amortProjectSite" disabled>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Lot Area</label>
                                                                    <input type="text" class="form-control" id="amortLotArea" disabled>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Price per sqm</label>
                                                                    <input type="text" class="form-control" id="amortPricePerSqm" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Phase, Block, Lot, Class</label>
                                                                    <input type="text" class="form-control" id="amortPhaseBlockLotClass" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Payment Terms (months)</label>
                                                                    <input type="text" class="form-control" id="amortTerms" disabled>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Total Contract Price</label>
                                                                    <input type="text" class="form-control" id="amortTotalContractPrice" disabled>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Monthly Amortization</label>
                                                                    <input type="text" class="form-control" id="amortMonthly" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Add’l Misc Fee (7%)</label>
                                                                    <input type="text" class="form-control" id="amortMiscFee" disabled>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Less Reservation</label>
                                                                    <input type="text" class="form-control" id="amortLessReservation" disabled>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Total Amount Payable</label>
                                                                    <input type="text" class="form-control" id="amortTotalPayable" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Net Selling Price</label>
                                                                    <input type="text" class="form-control" id="amortNetSellingPrice" disabled>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Balance Payable</label>
                                                                    <input type="text" class="form-control" id="amortBalancePayable" disabled>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- E. Payment Schedule Table -->
                                                <div class="card shadow-sm mb-4">
                                                    <div class="card-header bg-white fw-bold">Payment Schedule</div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered" id="paymentScheduleTable">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Due Date</th>
                                                                        <th>Amount Paid</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Populated by JS -->
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th class="text-end" colspan="2">Total Amount Paid:</th>
                                                                        <th id="totalAmountPaid"></th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- F. Notes/Reminders -->
                                                <div class="card shadow-sm mb-4">
                                                    <div class="card-header bg-white fw-bold">Notes & Reminders</div>
                                                    <div class="card-body">
                                                        <ul class="mb-2">
                                                            <li>Miscellaneous Fee must be paid in full upon turnover.</li>
                                                            <li>Late payments are subject to a 2% monthly penalty.</li>
                                                            <li>Reservation Fee is non-refundable and non-transferable.</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                           
                                            </div>
                                        </div> <!-- row end -->
                                    </div>

                                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                                    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
                                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                                    <script>
                                    // === SAMPLE DATA FOR DEMO ===
                                    const profileData = {
                                        client: {
                                            name: "Del Rosario, Ana M.",
                                            contact: "09171234567",
                                            email: "ana.rosario@email.com",
                                            civilStatus: "Married",
                                            dob: "1990-02-10",
                                            pob: "Quezon City",
                                            age: 34,
                                            nationality: "Filipino",
                                            religion: "Catholic",
                                            permAddress: "Blk 2 Lot 5, Sta. Maria Village, San Mateo, Rizal",
                                            provAddress: "Barangay Malaya, Pililla, Rizal",
                                            spouseName: "Del Rosario, Ben M.",
                                            spouseContact: "09179999999",
                                            spouseEmail: "ben.rosario@email.com"
                                        },
                                        employment: {
                                            companyName: "Acme Corp",
                                            companyAddress: "Makati Ave, Makati City",
                                            length: "5 years",
                                            position: "Engineer",
                                            contactPerson: "Jane Doe",
                                            contactPersonPosition: "HR Manager",
                                            contactNo: "8822-3000",
                                            email: "hr@acme.com",
                                            sssNo: "33-1234567-1",
                                            tinNo: "123-456-789"
                                        },
                                        property: {
                                            projectTitle: "Parkside Residences",
                                            projectSite: "Sta. Maria Village, San Mateo, Rizal",
                                            phaseNo: "2",
                                            blockNo: "5",
                                            lotNo: "8",
                                            lotArea: "350",
                                            lotClass: "Corner",
                                            pricePerSqm: "3000",
                                            agentContact: "Liza Manalo / 09181234567",
                                            manager: "Ana Cruz",
                                            broker: "Ramon Santos",
                                            reservationAmount: "20,000"
                                        },
                                        amort: {
                                            clientName: "Ana Del Rosario",
                                            reservationDate: "2023-08-01",
                                            projectTitle: "Parkside Residences",
                                            projectSite: "Sta. Maria Village, San Mateo, Rizal",
                                            lotArea: "350",
                                            pricePerSqm: "3000",
                                            phaseBlockLotClass: "2, 5, 8, Corner",
                                            terms: "48",
                                            totalContractPrice: "1,050,000",
                                            monthly: "22,000",
                                            miscFee: "73,500",
                                            lessReservation: "20,000",
                                            totalPayable: "1,128,500",
                                            netSellingPrice: "1,050,000",
                                            balancePayable: "1,088,500"
                                        },
                                        paymentSchedule: [
                                            {due: "2023-08-26", paid: "22,000", status: "Paid"},
                                            {due: "2023-09-26", paid: "22,000", status: "Paid"},
                                            {due: "2023-10-26", paid: "22,000", status: "Paid"},
                                            {due: "2023-11-26", paid: "22,000", status: "Unpaid"},
                                            {due: "2023-12-26", paid: "0", status: "Unpaid"}
                                        ]
                                    };

                                    // Fill data into fields
                                    function populateProfile(data) {
                                        // Personal
                                        $("#clientName").val(data.client.name);
                                        $("#clientContact").val(data.client.contact);
                                        $("#clientEmail").val(data.client.email);
                                        $("#civilStatus").val(data.client.civilStatus);
                                        $("#dob").val(data.client.dob);
                                        $("#pob").val(data.client.pob);
                                        $("#age").val(data.client.age);
                                        $("#nationality").val(data.client.nationality);
                                        $("#religion").val(data.client.religion);
                                        $("#permAddress").val(data.client.permAddress);
                                        $("#provAddress").val(data.client.provAddress);

                                        // Spouse
                                        $("#spouseName").val(data.client.spouseName);
                                        $("#spouseContact").val(data.client.spouseContact);
                                        $("#spouseEmail").val(data.client.spouseEmail);

                                        // Show/hide spouse card
                                        if(data.client.civilStatus === "Married") {
                                            $("#spouseCard").show();
                                        } else {
                                            $("#spouseCard").hide();
                                        }

                                        // Employment
                                        $("#companyName").val(data.employment.companyName);
                                        $("#companyAddress").val(data.employment.companyAddress);
                                        $("#lengthEmployment").val(data.employment.length);
                                        $("#position").val(data.employment.position);
                                        $("#companyContactPerson").val(data.employment.contactPerson);
                                        $("#contactPersonPosition").val(data.employment.contactPersonPosition);
                                        $("#companyContactNo").val(data.employment.contactNo);
                                        $("#companyEmail").val(data.employment.email);
                                        $("#sssNo").val(data.employment.sssNo);
                                        $("#tinNo").val(data.employment.tinNo);

                                        // Property
                                        $("#projectTitle").val(data.property.projectTitle);
                                        $("#projectSite").val(data.property.projectSite);
                                        $("#phaseNo").val(data.property.phaseNo);
                                        $("#blockNo").val(data.property.blockNo);
                                        $("#lotNo").val(data.property.lotNo);
                                        $("#lotArea").val(data.property.lotArea);
                                        $("#lotClass").val(data.property.lotClass);
                                        $("#pricePerSqm").val(data.property.pricePerSqm);
                                        $("#agentContact").val(data.property.agentContact);
                                        $("#manager").val(data.property.manager);
                                        $("#broker").val(data.property.broker);
                                        $("#reservationAmount").val(data.property.reservationAmount);

                                        // Amortization
                                        $("#amortClientName").val(data.amort.clientName);
                                        $("#reservationDate").val(data.amort.reservationDate);
                                        $("#amortProjectTitle").val(data.amort.projectTitle);
                                        $("#amortProjectSite").val(data.amort.projectSite);
                                        $("#amortLotArea").val(data.amort.lotArea);
                                        $("#amortPricePerSqm").val(data.amort.pricePerSqm);
                                        $("#amortPhaseBlockLotClass").val(data.amort.phaseBlockLotClass);
                                        $("#amortTerms").val(data.amort.terms);
                                        $("#amortTotalContractPrice").val(data.amort.totalContractPrice);
                                        $("#amortMonthly").val(data.amort.monthly);
                                        $("#amortMiscFee").val(data.amort.miscFee);
                                        $("#amortLessReservation").val(data.amort.lessReservation);
                                        $("#amortTotalPayable").val(data.amort.totalPayable);
                                        $("#amortNetSellingPrice").val(data.amort.netSellingPrice);
                                        $("#amortBalancePayable").val(data.amort.balancePayable);

                                        // Payment schedule
                                        let scheduleRows = data.paymentSchedule.map(p =>
                                            `<tr>
                                                <td>${p.due}</td>
                                                <td>${p.paid}</td>
                                                <td>${p.status === "Paid" ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Unpaid</span>'}</td>
                                            </tr>`
                                        ).join('');
                                        $("#paymentScheduleTable tbody").html(scheduleRows);
                                        // Total paid
                                        let totalPaid = data.paymentSchedule
                                            .filter(p => p.status === "Paid")
                                            .reduce((sum, p) => sum + parseFloat(p.paid.replace(/,/g, '') || 0), 0);
                                        $("#totalAmountPaid").text(totalPaid.toLocaleString());
                                        // Profile header
                                        $("#profileFullName").text(data.client.name);
                                        $("#profileEmail").text(data.client.email);
                                    }

                                    // --- Profile Editing Logic ---
                                    let fields = [
                                        "#clientName", "#clientContact", "#clientEmail", "#civilStatus", "#dob", "#pob", "#age", "#nationality", "#religion", "#permAddress", "#provAddress",
                                        "#spouseName", "#spouseContact", "#spouseEmail",
                                        "#companyName", "#companyAddress", "#lengthEmployment", "#position", "#companyContactPerson", "#contactPersonPosition", "#companyContactNo", "#companyEmail", "#sssNo", "#tinNo"
                                    ];

                                    function setEditable(editable) {
                                        for (let selector of fields) {
                                            $(selector).prop("disabled", !editable);
                                        }
                                        $("#btn-edit-profile").toggleClass("d-none", editable);
                                        $("#btn-save-profile, #btn-cancel-edit").toggleClass("d-none", !editable);
                                    }

                                    $("#btn-edit-profile").on("click", function() {
                                        setEditable(true);
                                    });
                                    $("#btn-cancel-edit").on("click", function() {
                                        setEditable(false);
                                        populateProfile(profileData);
                                    });
                                    $("#btn-save-profile").on("click", function() {
                                        setEditable(false);
                                        // You would gather updated field values and send to server here
                                        // For demo, just keep showing edited fields
                                    });

                                    // Initialize with sample data
                                    $(function() {
                                        populateProfile(profileData);
                                        setEditable(false);
                                    });
                                    </script>

                            </div>
                        </div>
                    </div>

                </div> <!-- container -->

            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include 'layouts/customer-portal-sidebar.php'; ?>

    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>