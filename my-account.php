<?php

include 'layouts/session.php';
include 'layouts/main.php';
require_once __DIR__ . '/layouts/config.php'; // Database connection

// Defaults
$user_name = "User";
$user_role = "";
$user_avatar = "avatar-default.jpg";

// All profile fields
$contact = [];
$employment = [];

if (isset($_SESSION['user_id'])) {
    // 1. Get contact_id from users table
    $sql = "SELECT contact_id FROM users WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($contact_id);
    $stmt->fetch();
    $stmt->close();

    if ($contact_id) {
        // 2. Fetch contact data
        $sql = "
            SELECT
                first_name,
                last_name,
                email,
                phone_number,
                civil_status,
                date_of_birth,
                place_of_birth,
                age,
                nationality,
                religion,
                permanent_address,
                provincial_address,
                spouse_name,
                spouse_contact_number,
                spouse_email,
                profile_image
            FROM contacts
            WHERE id = ?
        ";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        $stmt->bind_result(
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $civil_status,
            $date_of_birth,
            $place_of_birth,
            $age,
            $nationality,
            $religion,
            $permanent_address,
            $provincial_address,
            $spouse_name,
            $spouse_contact_number,
            $spouse_email,
            $profile_image
        );

        if ($stmt->fetch()) {
            $user_name = $first_name;
            $user_role = $email;

            $contact = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone_number' => $phone_number,
                'civil_status' => $civil_status,
                'date_of_birth' => $date_of_birth,
                'place_of_birth' => $place_of_birth,
                'age' => $age,
                'nationality' => $nationality,
                'religion' => $religion,
                'permanent_address' => $permanent_address,
                'provincial_address' => $provincial_address,
                'spouse_name' => $spouse_name,
                'spouse_contact_number' => $spouse_contact_number,
                'spouse_email' => $spouse_email,
                'profile_image' => $profile_image
            ];

            if (!empty($profile_image)) {
                $user_avatar = $profile_image;
            }
        }
        $stmt->close();

        // 3. Fetch employment data
        $sql = "
            SELECT
                company_name,
                company_address,
                position,
                length_of_employment,
                company_contact_person,
                contact_person_position,
                company_contact_number,
                company_contact_email,
                sss_umid_number,
                tin_number
            FROM employment_data
            WHERE contact_id = ?
            LIMIT 1
        ";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        $stmt->bind_result(
            $company_name,
            $company_address,
            $position,
            $length_of_employment,
            $company_contact_person,
            $contact_person_position,
            $company_contact_number,
            $company_contact_email,
            $sss_umid_number,
            $tin_number
        );

        if ($stmt->fetch()) {
            $employment = [
                'company_name' => $company_name,
                'company_address' => $company_address,
                'position' => $position,
                'length_of_employment' => $length_of_employment,
                'company_contact_person' => $company_contact_person,
                'contact_person_position' => $contact_person_position,
                'company_contact_number' => $company_contact_number,
                'company_contact_email' => $company_contact_email,
                'sss_umid_number' => $sss_umid_number,
                'tin_number' => $tin_number
            ];
        }

        $stmt->close();
    }
}
?>





<head>
    <title>My Account | Client Portal</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .account-profile-card {
            background: #f6f8fb;
            border-radius: 14px;
            box-shadow: 0 2px 10px #0001;
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 64px;
            height: 64px;
            background: #eaf0fa;
            border-radius: 50%;
            font-size: 2.5rem;
            color: #385a99;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .section-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #254680;
            margin-bottom: 1rem;
        }
        .card-section {
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
        }
        .form-label {
            font-weight: 500;
        }
        .edit-actions {
            margin-top: 24px;
        }
    </style>
</head>
<?php include 'portal-nav.php'; ?>
<body class="authentication-bg">

<?php include 'layouts/background.php'; ?>

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Profile Header Card -->
                <div class="account-profile-card p-4 mb-4 d-flex align-items-center gap-4 flex-wrap">
                    <div class="profile-avatar"><i class="ri-user-3-line"></i></div>
                    <div>
                        <div class="fs-4 fw-bold mb-1" id="accProfileName"><?= htmlspecialchars($user_name) ?></div>
                        <div class="text-muted" id="accProfileEmail"><?= htmlspecialchars($email) ?></div>
                    </div>
                    <div class="ms-auto">
                        <button class="btn btn-outline-primary" id="btn-edit-account"><i class="ri-edit-2-line"></i> Edit Account</button>
                        <button class="btn btn-success d-none" id="btn-save-account"><i class="ri-save-2-line"></i> Save</button>
                        <button class="btn btn-outline-secondary d-none" id="btn-cancel-edit"><i class="ri-close-line"></i> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <form id="myAccountForm">
            <div class="row g-4">
                <!-- Personal Info -->
                <div class="col-lg-6">
                    <div class="card card-section mb-3 p-3">
                        <div class="section-title">Personal Information</div>
                        <div class="mb-2">
                            <label class="form-label">Contact No.</label>
                            <input type="text" class="form-control" id="clientContact" disabled value=" <?php echo $contact['phone_number']; ?>">
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Civil Status</label>
                                <select class="form-select" id="civilStatus" disabled>
                                    <option>Single</option>
                                    <option selected>Married</option>
                                    <option>Widowed</option>
                                    <option>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" disabled value="<?php echo $contact['date_of_birth']; ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Place of Birth</label>
                                <input type="text" class="form-control" id="pob" disabled value="<?php echo $contact['place_of_birth']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" disabled value="<?php echo $contact['age']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" disabled value=" <?php echo $contact['nationality']; ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Religion</label>
                            <input type="text" class="form-control" id="religion" disabled value=" <?php echo $contact['religion']; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Permanent Address</label>
                            <input type="text" class="form-control" id="permAddress" disabled value=" <?php echo $contact['permanent_address']; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Provincial Address</label>
                            <input type="text" class="form-control" id="provAddress" disabled value="Barangay Malaya, Pililla, Rizal">
                        </div>
                    </div>
                </div>
                <!-- Spouse & Employment Info -->
                <div class="col-lg-6">
                    <!-- Spouse Info (show/hide if Married) -->
                    <div class="card card-section mb-3 p-3" id="spouseCard">
                        <div class="section-title">Spouse Information</div>
                        <div class="mb-2">
                            <label class="form-label">Spouse's Name</label>
                            <input type="text" class="form-control" id="spouseName" disabled value="<?php echo $contact['spouse_name']; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Spouse's Contact No.</label>
                            <input type="text" class="form-control" id="spouseContact" disabled value="<?php echo $contact['spouse_contact_number']; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Spouse's Email</label>
                            <input type="email" class="form-control" id="spouseEmail" disabled value="<?php echo $contact['spouse_email']; ?>">
                        </div>
                    </div>
                    <!-- Employment Info -->
                    <div class="card card-section mb-3 p-3">
                        <div class="section-title">Employment Data</div>
                        <div class="mb-2">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="companyName" disabled value="<?php echo $employment['company_name']; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Company Address</label>
                            <input type="text" class="form-control" id="companyAddress" disabled value="<?php echo $employment['company_address']; ?>">
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Length of Employment</label>
                                <input type="text" class="form-control" id="lengthEmployment" disabled value="<?php echo $employment['length_of_employment']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" disabled value="<?php echo $employment['position']; ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Company Contact Person</label>
                            <input type="text" class="form-control" id="companyContactPerson" disabled value="<?php echo $employment['company_contact_person']; ?>">
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Contact Person Position</label>
                                <input type="text" class="form-control" id="contactPersonPosition" disabled value="<?php echo $employment['contact_person_position']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact No.</label>
                                <input type="text" class="form-control" id="companyContactNo" disabled value="<?php echo $employment['company_contact_number']; ?>">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="companyEmail" disabled value="<?php echo $employment['company_contact_email']; ?>">
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label">SSS/UMID No.</label>
                                <input type="text" class="form-control" id="sssNo" disabled value="<?php echo $employment['sss_umid_number']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TIN</label>
                                <input type="text" class="form-control" id="tinNo" disabled value="<?php echo $employment['tin_number']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Actions -->
            <div class="row">
                <div class="col-12 edit-actions d-none" id="accountEditActions">
                    <div class="d-flex gap-3 justify-content-end">
                        <button class="btn btn-success" id="btn-save-form" type="submit"><i class="ri-save-2-line"></i> Save Changes</button>
                        <button class="btn btn-outline-secondary" id="btn-cancel-form" type="button"><i class="ri-close-line"></i> Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div><!-- end container -->
</div><!-- end account-pages -->

<footer class="footer footer-alt fw-medium">
    <span class="bg-body"><script>document.write(new Date().getFullYear())</script> © Attex - Coderthemes.com</span>
</footer>

<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/js/app.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // Toggle form editability
    $("#btn-edit-account").on("click", function() {
        $("#myAccountForm input, #myAccountForm select").prop("disabled", false);
        $("#btn-edit-account").addClass("d-none");
        $("#btn-save-account, #btn-cancel-edit, #accountEditActions").removeClass("d-none");
    });
    $("#btn-cancel-edit, #btn-cancel-form").on("click", function() {
        $("#myAccountForm")[0].reset();
        $("#myAccountForm input, #myAccountForm select").prop("disabled", true);
        $("#btn-edit-account").removeClass("d-none");
        $("#btn-save-account, #btn-cancel-edit, #accountEditActions").addClass("d-none");
    });
    // Hide spouse card if civil status is not Married
    $("#civilStatus").on("change", function() {
        if ($(this).val() === "Married") {
            $("#spouseCard").show();
        } else {
            $("#spouseCard").hide();
        }
    });
    // Save button (for demo)
    $("#btn-save-account, #btn-save-form").on("click", function(e) {
        e.preventDefault();
        $("#myAccountForm input, #myAccountForm select").prop("disabled", true);
        $("#btn-edit-account").removeClass("d-none");
        $("#btn-save-account, #btn-cancel-edit, #accountEditActions").addClass("d-none");
        // Add AJAX save here as needed
    });
</script>
</body>
</html>
