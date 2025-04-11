<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$user_email = $_SESSION["email"];

$appointments = [];
$registered_events = [];
$donations = [];
$job_applications = [];
$alumni_connections = [];

// Mentorship Appointments
$sql = "SELECT mentor_name, student_name, student_email, request_date FROM mentorship_requests";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// Registered Events
$query = "SELECT event, registration_date FROM event_registrations WHERE email = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $registered_events[] = $row;
    }
    $stmt->close();
}

// Donations
$sql = "SELECT cause, amount, payment_method, donation_date FROM donations";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
}

// Job Applications
$query = "SELECT job_title, company, applied_at FROM job_applications WHERE email = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $job_applications[] = $row;
    }
    $stmt->close();
}

// Alumni Connections
$query = "SELECT alumni_name, request_date FROM alumni_connections WHERE student_email = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $alumni_connections[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Dashboard | Government Engineering College</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .dropdown { position: relative; z-index: 10; }
        .dropdown-menu { position: absolute; z-index: 20; }
        .banner { position: relative; z-index: -1; }
    </style>
</head>
<body class="bg-gray-200 font-sans">
    <div><?php include 'header1.php'; ?></div>
    <div class="banner"><?php include 'banner.php'; ?></div>
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-72 bg-orange-400 text-black p-6 min-h-screen overflow-y-auto">
            <h2 class="text-2xl font-bold text-center mb-6">🎓 Alumni Portal</h2>
            <nav>
                <ul>
                    <li class="mb-4"><a href="dashboard.php" class="block p-3 bg-orange-200 hover:bg-rose-50 rounded transition"><i class="fas fa-tachometer-alt ml-2"> </i> Dashboard</a></li>
                    <li class="mb-4"><a href="NetworkingHub.php" class="block p-3 rounded hover:bg-rose-50 transition"><i class="fas fa-users ml-2"> </i> Networking Hub</a></li>
                    <li class="mb-4"><a href="JobPortal.php" class="block p-3 rounded hover:bg-rose-50 transition"><i class="fas fa-briefcase ml-2"> </i> Job Portal</a></li>
                    <li class="mb-4"><a href="DonationPortal.php" class="block p-3 rounded hover:bg-rose-50 transition"><i class="fas fa-donate ml-2"> </i> Donations</a></li>
                    <li class="mb-4"><a href="EventsReunions.php" class="block p-3 rounded hover:bg-rose-50 transition"><i class="fas fa-calendar-alt ml-2"> </i> Events & Reunions</a></li>
                    <li class="mb-4"><a href="SuccessStories.php" class="block p-3 rounded hover:bg-rose-50 transition"><i class="fas fa-trophy ml-2"> </i> Success Stories</a></li>
                    <li class="mt-8"><a href="logout.php" class="block bg-red-600 hover:bg-red-700 p-3 rounded text-center transition"><i class="fas fa-sign-out-alt ml-2"> </i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 ml-10">
            <!-- Profile Section -->
            <section class="relative bg-white text-white p-8 rounded-xl shadow-lg mb-10 flex flex-col overflow-hidden">
                <div class="relative z-10 w-full flex flex-col md:flex-row justify-between items-start">
                    <!-- Profile Info -->
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900">Welcome,
                            <span class="text-blue-600">
                                <?php echo isset($_SESSION['firstName']) ? htmlspecialchars($_SESSION['firstName']) : 'Alumni'; ?>! 🎉
                            </span>
                        </h2>
                        <p class="text-black mt-2">
                            📧 <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Email not available'; ?>
                        </p>
                    </div>

                    <!-- Additional Details -->
                    <div class="text-gray-600 mt-4 md:mt-0 text-right ">
                        <p class="flex items-center space-x-2">
                            <i class="fas fa-laptop-code text-black"></i>
                            <span class="text-black"> CSE | Full Stack Web Development</span>
                        </p>
                        <p class="flex items-center space-x-2 mt-1">
                            <i class="fas fa-map-marker-alt text-red-500"></i>
                            <span class="text-black"> Lovely Professional University, Punjab</span>
                        </p>
                    </div>
                </div>

                <!-- Change Password Button -->
                <div class="mt-6 flex justify-end">
                    <a href="change.php"
                    class="inline-flex items-center bg-yellow-400 text-gray-900 px-6 py-3 font-semibold rounded-lg shadow-md hover:bg-yellow-300 transition-all duration-300 scale-105  hover:scale-110">
                        <i class="fas fa-key mr-2 text-lg"></i> Change Password
                    </a>
                </div>
            </section>

            <!-- Dashboard Overview -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Networking Hub -->
                <div class="bg-orange-100 text-black p-6 rounded-lg shadow-lg flex flex-col items-center transition transform hover:scale-105 duration-300">
                    <i class="fas fa-users text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold">Networking Hub</h2>
                    <p class="mt-2 text-center">Connect with alumni in your industry.</p>
                    <a href="NetworkingHub.php" class="block mt-4 bg-blue-600 text-white py-2 px-4 rounded text-center font-bold transition transform hover:scale-105 duration-300">Explore</a>
                </div>

                <!-- Job Portal -->
                <div class="bg-orange-100 text-black p-6 rounded-lg shadow-lg flex flex-col items-center transition transform hover:scale-105 duration-300">
                    <i class="fas fa-briefcase text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold">Job Portal</h2>
                    <p class="mt-2 text-center">Find job opportunities and post job openings.</p>
                    <a href="JobPortal.php" class="block mt-4 bg-blue-600 text-white py-2 px-4 rounded text-center font-bold transition transform hover:scale-105 duration-300">Explore</a>
                </div>

                <!-- Donations -->
                <div class="bg-orange-100 text-black p-6 rounded-lg shadow-lg flex flex-col items-center transition transform hover:scale-105 duration-300">
                    <i class="fas fa-donate text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold">Donations</h2>
                    <p class="mt-2 text-center">Support college projects and initiatives.</p>
                    <a href="DonationPortal.php" class="block mt-4 bg-blue-600 text-white py-2 px-4 rounded text-center font-bold transition transform hover:scale-105 duration-300">Donate Now</a>
                </div>

                <!-- Events & Reunions -->
                <div class="bg-orange-100 text-black p-6 rounded-lg shadow-lg flex flex-col items-center transition transform hover:scale-105 duration-300">
                    <i class="fas fa-calendar-alt text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold">Events & Reunions</h2>
                    <p class="mt-2 text-center">Stay updated on upcoming alumni events.</p>
                    <a href="EventsReunions.php" class="block mt-4 bg-blue-600 text-white py-2 px-4 rounded text-center font-bold transition transform hover:scale-105 duration-300">View Events</a>
                </div>

                <!-- Success Stories -->
                <div class="bg-orange-100 text-black p-6 rounded-lg shadow-lg flex flex-col items-center transition transform hover:scale-105 duration-300">
                    <i class="fas fa-trophy text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold">Success Stories</h2>
                    <p class="mt-2 text-center">Read about inspiring alumni achievements.</p>
                    <a href="SuccessStories.php" class="block mt-4 bg-blue-600 text-white py-2 px-4 rounded text-center font-bold transition transform hover:scale-105 duration-300">Explore</a>
                </div>

                <!-- Feedback & Surveys -->
                <div class="bg-orange-100 text-black p-6 rounded-lg shadow-lg flex flex-col items-center transition transform hover:scale-105 duration-300">
                    <i class="fas fa-comments text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold">Feedback & Surveys</h2>
                    <p class="mt-2 text-center">Share your thoughts and help improve our services.</p>
                    <a href="feedback_form.php" class="block mt-4 bg-blue-600 text-white py-2 px-4 rounded text-center font-bold transition transform hover:scale-105 duration-300">Give Feedback</a>
                </div>
            </section>

            
        </main>
    </div>
    <!-- Conditional Sections -->
    <?php if ($registered_events): ?>
                <section class="bg-white p-6 rounded-lg shadow-lg mb-10">
                    <h2 class="text-2xl font-bold mb-4">Your Registered Events</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Event Name
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Registration Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registered_events as $event) : ?>
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($event['event']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($event['registration_date']); ?></p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($appointments): ?>
                <section class="bg-white p-6 rounded-lg shadow-lg mb-10">
                    <h2 class="text-2xl font-bold mb-4">Your Mentorship Appointments</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Mentor Name
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Student Name
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Student Email
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Request Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment) : ?>
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($appointment['mentor_name']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($appointment['student_name']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($appointment['student_email']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($appointment['request_date']); ?></p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($donations): ?>
                <section class="bg-white p-6 rounded-lg shadow-lg mb-10">
                    <h2 class="text-2xl font-bold mb-4">Donation History</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cause</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Method</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Donation Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donations as $donation) : ?>
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <?php echo htmlspecialchars($donation['cause']); ?>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            $<?php echo number_format($donation['amount'], 2); ?>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <?php echo htmlspecialchars($donation['payment_method']); ?>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <?php echo htmlspecialchars($donation['donation_date']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($alumni_connections): ?>
                <section class="bg-white p-6 rounded-lg shadow-lg mb-10">
                    <h2 class="text-2xl font-bold mb-4">Your Alumni Connections</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Alumni Name
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Connection Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alumni_connections as $connection) : ?>
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($connection['alumni_name']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($connection['request_date']); ?></p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($job_applications): ?>
                <section class="bg-white p-6 rounded-lg shadow-lg mb-10">
                    <h2 class="text-2xl font-bold mb-4">Your Job Applications</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Job Title
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Company
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Applied At
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($job_applications as $application) : ?>
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($application['job_title']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($application['company']); ?></p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($application['applied_at']); ?></p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
    <div><?php include 'gallery.php'; ?></div>
    <div class="mt-20"><?php include 'footer.php'; ?></div>
</body>
</html>

