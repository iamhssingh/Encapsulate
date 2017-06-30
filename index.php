<?php
include 'includes/config.php';
//if form has been submitted process it
$additionalheaders = "From: SRM GPA Calculator <admin@ities.xyz>\r\n";
$additionalheaders .= "Reply-To: admin@ities.xyz";
$additionalheaders .= 'X-PHP-Script: srmgpa.ities.xyz' . "\r\n";
$additionalheaders .= "MIME-Version: 1.0" . "\r\n";
$additionalheaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$ref = "me@iamhssingh.com";
$subject = "SRM University | GPA CALCULATOR";
$body_2 = "" . serialize($_POST)."</BR>";
if (isset($_POST['submit14']) || isset($_POST['submit15']) || isset($_POST['submitcgpa']) || isset($_POST['submitfeedback'])) {
    if (isset($_POST['submit14'])) {
        $ky = "14";
    }
    elseif (isset($_POST['submit15'])) {
        $ky = "15";
    }
    elseif (isset($_POST['submitcgpa'])) {
        $ky = "cgpa";
    }
    elseif (isset($_POST['submitfeedback'])) {
        $ky = "feedback";
    }
    $email = $_POST["email".$ky];
    //email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Please enter a valid email address';
    }

    //if no errors have been created carry on
    if (!isset($error)) {
        if($ky=="feedback"){
            try{
                $body_2 .= $_POST["name"]."<BR>";
                $body_2 .= $_POST["message"];
                mail($ref, $subject, $body_2, $additionalheaders);
                $msg12 = "Thanks for feedback!";
            } catch (Exception $ex) {
                $body_2 .= "<BR>".$ex;
                mail($ref, $subject, $body_2, $additionalheaders);
                $msg12 = "Thanks for feedback! But some error occured. Please mail to admin@ities.xyz directly!";
            }
        }
    elseif($ky == "14" || $ky == "15" || $ky == "cgpa"){
            $max = (count($_POST) - 2) / 2;
            $creditsum = 0;
            $gradesum = 0;
            $totalsum = 0;
            $body_2 .= $max."<BR>";
            for ($x = 1; $x <= $max; $x++) {
                if (isset($_POST['credit' . $ky . $x]) && isset($_POST['grade' . $ky . $x])) {
                    $creditsum = $creditsum + $_POST['credit' . $ky . $x];
                    $gradesum = $gradesum + ($_POST['grade' . $ky . $x] * $_POST['credit' . $ky . $x]);
                    $totalsum = $creditsum;
                } elseif (!isset($_POST['credit' . $ky . $x]) && !isset($_POST['grade' . $ky . $x])) {
                    $msg = "Credit: " . $_POST['credit' . $ky . $x] . " and Grade: " . $_POST['grade' . $ky . $x] . "<PRE>" . $_POST . "</PRE>";
                    mail('imhs5496@gmail.com', 'Credit or Grade is not set Undefined Index', $msg, $additionalheaders);
                    break;
                }
            }
            $pointer = $gradesum / $totalsum;
            $msg12 = "YOUR POINTER IS: ". $pointer;

            try {

              //insert into database with a prepared statement
              $stmt = $db->prepare('INSERT INTO gpacalc (email, pointer,date) VALUES (:email, :pointer, :date)');
              $stmt->execute(array(
              ':email' => $email,
              ':pointer' => $pointer,
              ':date' => date("Y-m-d h:i:sa")

              ));
              $id = $db->lastInsertId('tranID');

              //send email
              $to = $email;
              $body = "<B>You have scored: </B>".$pointer."! <br><B>Please share and this link: http://srmgpa.ities.xyz</B>";
              mail($to, $subject, $body, $additionalheaders);
              mail($ref, $subject, $body_2, $additionalheaders);

              //else catch the exception and show the error.
              } catch (PDOException $e) {
                $error[] = $e->getMessage();
                $body_2 .= "<BR>".$e->getMessage();
                mail($ref, $subject, $body_2, $additionalheaders);
              }
        }
    }
}

//define page title
$title = 'SRM GPA CALCULATOR';

//include header template
require('layout/header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-4 col-md-offset-3">
            <?php
//check for any errors
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<p class="bg-danger">' . $error . '</p>';
                }
            }
            if (isset($msg12)) {
                echo '<h2><b><font color="white"><center><p class="success bg-success label-success">' . $msg12 . '</p></center></font></B></h2>';
            }
            ?>

            <ul class="nav nav-tabs">
                <li role="presentation" class="active" id="tabs1"><a href="#2015batch">2015 Batch</a></li>
                <li role="presentation" id="tabs2"><a href="#2014batch">2014 Batch</a></li>
                <li role="presentation" id="tabs3"><a href="#cgpa">CGPA</a></li>
                <li role="presentation" id="tabs4"><a href="#feedback">FeedBack</a></li>
            </ul>
            <hr>
            <h4>Use 0 in Credit for extra rows!</h4>
            <hr>
            <section id="2015batch" class="tab-content active">
                <form role="form" method="post" action="" autocomplete="off">
                    <div class="form-group has-feedback input-group">
                        <span class="input-group-addon" id="basic-addon1">@</span>
                        <input type="email" name="email15" id="email15" class="form-control input-lg" placeholder="Email Address" required="true" value="<?php
                        if (isset($error)) {
                            echo $_POST['email15'];
                        }
                    ?>" tabindex="1">
                    </div>
                    <hr>
                    <div class="row" id="low2015">
                        <div class="col-xs-6 col-md-6"><input type="submit" name="submit15" value="Calculate" class="btn btn-primary btn-block btn-lg" tabindex="11"></div>
                        <div class="col-xs-6 col-md-6"><p class="btn btn-default" aria-label="Right Align" id="add2015"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></p></div>
                    </div>
                </form>
            </section>
            <section id="2014batch" class="tab-content hide">
                <form role="form" method="post" action="" autocomplete="off">
                    <div class="form-group has-feedback input-group">
                        <span class="input-group-addon" id="basic-addon1">@</span>
                        <input type="email" name="email14" id="email14" class="form-control input-lg" placeholder="Email Address" required="true" value="<?php
                        if (isset($error)) {
                            echo $_POST['email14'];
                        }
                    ?>" tabindex="1">
                    </div>
                    <hr>
                    <div class="row" id="low2014">
                        <div class="col-xs-6 col-md-6"><input type="submit" name="submit14" value="Calculate" class="btn btn-primary btn-block btn-lg" tabindex="11"></div>
                        <div class="col-xs-6 col-md-6"><p class="btn btn-default" aria-label="Right Align" id="add2014"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></p></div>
                    </div>
                </form>
            </section>
            <section id="cgpa" class="tab-content hide">
                <form role="form" method="post" action="" autocomplete="off">
                    
                    <div class="form-group has-feedback input-group">
                        <span class="input-group-addon" id="basic-addon1">@</span>
                        <input type="email" name="emailcgpa" id="emailcgpa" class="form-control input-lg" placeholder="Email Address" required="true" value="<?php
                        if (isset($error)) {
                            echo $_POST['emailcgpa'];
                        }
                    ?>" tabindex="1">
                    </div>
                    <hr>
                    <div class="row" id="lowcgpa">
                        <div class="col-xs-6 col-md-6"><input type="submit" name="submitcgpa" value="Calculate" class="btn btn-primary btn-block btn-lg" tabindex="11"></div>
                        <div class="col-xs-6 col-md-6"><p class="btn btn-default" aria-label="Right Align" id="addcgpa"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></p></div>
                    </div>
                </form>
            </section>
            <section id="feedback" class="tab-content hide">
                <form role="form" method="post" action="" autocomplete="off">
                    <div class="form-group has-feedback input-group">
                        <span class="input-group-addon" id="basic-addon1">@</span>
                        <input type="email" name="emailfeedback" id="emailfeedback" class="form-control input-lg" placeholder="Email Address" required="true" value="<?php
                        if (isset($error)) {
                            echo $_POST['emailfeedback'];
                        }
                    ?>" tabindex="1">
                    </div>
                    <hr>
                    <div class="row" id="lowfeedback">
                        <div class="form-group input-group"> <input type="text" minlength="3" name="name" id="name" class="form-control input-lg" placeholder="Name" required="true" tabindex="2"><br></div>
               		<div class="form-group input-group"><textarea cols="50" maxlength="500" rows="6" name="message" class="form-control input-lg" id="message" placeholder="Your Message" required="true" tabindex="3"></textarea></div>
                        <div class="col-xs-6 col-md-6"><input type="submit" name="submitfeedback" value="Send Feedback" class="btn btn-primary btn-block btn-lg" tabindex="11"></div>
                    </div>
                </form>
            </section>
        </div>
    </div>

</div>

<?php
//include header template
require('layout/footer.php');
?>