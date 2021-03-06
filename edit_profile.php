<?php
/*
    Edit a user profile.
    Should only be allowed by a profile owner.
*/

require_once 'header.php';
require_once 'navbar.php';

if(userLoggedIn() == false){
    //User is not logged in. Redirect the browser to the login index.php page and kill this page.
    header("Location: index.php");
    die();
}


// SELECT all user info query
$query = "SELECT FName, LName, gender, email, phone_no, grad_year, faculty_id, degree_type FROM users WHERE user_id = ?";

$stmt = $con->prepare($query);
$stmt->bind_Param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Row data
$row = $result->fetch_assoc();


// Get Faculty name from Facutlies table
$query = "SELECT faculty FROM faculties WHERE faculty_id = ?";

$stmt = $con->prepare($query);
$stmt->bind_Param("i", $row['faculty_id']);
$stmt->execute();
$result = $stmt->get_result();

$facultyName = $result->fetch_assoc();


$FName = $row['FName'];
$LName = $row['LName'];
$gender = $row['gender'];
$email= $row['email'];
$phone_no = $row['phone_no'];
$grad_year = $row['grad_year'];
$faculty_id = $row['faculty_id'];
$degree_type =$row['degree_type'];


if(isset($_POST['submitBtn'])){

    $userMsg = '';
    $validForm = True;

    // Validation Errors
    $emailError = '';
    $passError = '';


    // POST Values
    $FName = $_POST['FName'];
    $LName = $_POST['LName'];
    $gender = $_POST['gender'];
    $email= $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['re-password'];
    $phone_no = $_POST['phone_no'];
    $grad_year = $_POST['grad_year'];
    $faculty_id = $_POST['faculty_id'];
    $degree_type =$_POST['degree_type'];



    //Check if the Email is a valid email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $validForm = false;
        $emailError = '<br><p class="alert alert-danger text-center">Please enter a valid Email</p>';
    }
    // else
    // { // If the email was valid, then check if it already exists in the db.
    //     $query = "SELECT email FROM users WHERE email=?";

    //     // prepare query for execution
    //     if($stmt = $con->prepare($query)){

    //         $stmt->bind_Param("s", $_POST['email']);
    //         $stmt->execute();
    //         $result = $stmt->get_result();
    //         $num = $result->num_rows;

    //         // If an email is found
    //         if($num>0){
    //             $emailError = '<br><p class="alert alert-danger text-center">This email is already registered!</p>';
    //             $validForm = false;
    //         }
    //     }
    // }


    // Check if the password match
    if($password != $repassword){
        $passError = '<br><p class="alert alert-danger text-center">The passwords do not match</p>';
        $validForm = false;
    }



    // At this point we should have checked all the user input for errors
    if ($validForm == true){
        $query = "UPDATE `users` SET `FName` = ?, `LName` = ?, `gender` = ?, `email` = ?,
                        `password` = ?, `phone_no` = ?, `grad_year` = ?, `faculty_id` = ?,
                        `degree_type` = ? WHERE `user_id` = ?";

        $stmt = $con->prepare($query);

        $stmt->bind_param("sssssiiisi", $FName, $LName, $gender, $email, $password, $phone_no, $grad_year, $faculty_id, $degree_type, $_SESSION['user_id']);


        if ($stmt->execute()){
            echo '<script type="text/javascript">
            alert("Your profile has been updated!");
            window.location.href = "profile.php";
            </script>';

            // $userMsg = '<br><p class="alert alert-success text-center">Updated Successfully</p>';

        }
        else{
            $userMsg = '<br><p class="alert alert-danger text-center">Execute Failed</p>';
        }
    }
    else{
        $userMsg = '<br><p class="alert alert-danger text-center">Failed to create account</p>';
    }

}


?>

<!DOCTYPE HTML>
<html>

<head>
</head>

<body>

    <form action="edit_profile.php" method="post">
    <div class = "container col-md-6 col-md-offset-3">

            <h1 class = "form-signin-heading text-center">Edit your profile</h1>
            <hr class = "colorgraph"> <br>

            <?php echo isset($userMsg)? $userMsg: '' ?>

            First Name: <input type="text" class="form-control" name="FName" placeholder="First Name" value="<?php echo !empty($FName)?$FName:'' ; ?>" maxlength="10" required autofocus=""/>

            <br>Last Name: <input type="text" class="form-control" name="LName" placeholder="Last Name" maxlength="10" value="<?php echo !empty($LName)? $LName: '' ; ?>" required/>

            <br>Email: <input type="text"  class="form-control" name="email" placeholder="Email" maxlength="32" value="<?php echo !empty($email)? $email: '' ; ?>" required />
            <?php echo isset($emailError)? $emailError: '' ?>

            <br>Password: <input type="password" class="form-control" name="password" placeholder="Password" maxlength="32" required/>

            <br>Re-Type Password: <input type="password" class="form-control" name="re-password" placeholder = "Retype Password" maxlength="32" required/>
            <?php echo isset($passError)? $passError: '' ?>

            <br>Gender:<br>
            <label class="radio-inline"><input type="radio" name="gender" value="Female" checked="checked">Female</label>
            <label class="radio-inline"><input type="radio" name="gender" value="Male">Male</label><br>

            <br>Phone Number: <input type="text" class="form-control" name="phone_no" placeholder="1234567890" maxlength="10" value="<?php echo !empty($phone_no)? $phone_no: '' ; ?>" required/>

            <br>Graduation Year: <input type="text" class="form-control" name="grad_year" placeholder="2016" maxlength="4" value="<?php echo !empty($grad_year)? $grad_year: '' ; ?>" required/>

            <br>Faculty:
            <select class="form-control" name="faculty_id">
                <option value="1">Arts and Science</option>
                <option value="2">Education</option>
                <option value="3">Engineering and Applied Science</option>
                <option value="4">Commerce</option>
            </select>

            <br>Degree Type:
            <select class="form-control" name="degree_type">
                <option value="BA">BA</option>
                <option value="BCMP">BCMP</option>
                <option value="BFA">BFA</option>
                <option value="BMUS">BMUS</option>
                <option value="BPHE">BPHE</option>
                <option value="BSC">BSC</option>
            </select><br>

            <br><input type="submit" class="btn btn-lg btn-primary btn-block" name="submitBtn" value="Update Profile" /><br>
    </div>
    </form>

</body>
</html>
