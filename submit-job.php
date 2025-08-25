<?php
// DB connection (replace with your DB info)
$conn = new mysqli("localhost", "root", "", "gbt");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$name     = $_POST['name'];
$email    = $_POST['email'];
$phone    = $_POST['phone'];
$position = $_POST['position'];
$message  = $_POST['message'];

// Handle file upload
$targetDir = "uploads/";
$resumeName = basename($_FILES["resume"]["name"]);
$targetFile = $targetDir . time() . "_" . $resumeName;
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Validate file type
$allowedTypes = ['pdf', 'doc', 'docx'];
if (!in_array($fileType, $allowedTypes)) {
  echo "<script>alert('Only PDF, DOC, DOCX files are allowed.'); window.location.href='careers.php';</script>";
  exit;
}

// Upload file
if (move_uploaded_file($_FILES["resume"]["tmp_name"], $targetFile)) {
  // Insert into DB
  $stmt = $conn->prepare("INSERT INTO job_applications (name, email, phone, position, message, resume_path) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $name, $email, $phone, $position, $message, $targetFile);

  if ($stmt->execute()) {
    echo "<script>alert('Application submitted successfully!'); window.location.href='careers.php';</script>";
  } else {
    echo "<script>alert('Error saving to database.'); window.location.href='careers.php';</script>";
  }

  $stmt->close();
} else {
  echo "<script>alert('There was an error uploading your resume.'); window.location.href='careers.php';</script>";
}

$conn->close();
?>
