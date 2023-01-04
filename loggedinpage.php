<?php 
  session_start();

  $diaryContent = "";

  if (array_key_exists("id", $_COOKIE)) {    
    $_SESSION['id'] = $_COOKIE['id'];
  }

  if(array_key_exists("id", $_SESSION)) {
    include('connection.php');

    $id = mysqli_real_escape_string($link, $_SESSION['id']);
    $query = "SELECT diary FROM users WHERE id = " . $id . " LIMIT 1";

    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);

    $diaryContent = $row['diary'];

  } else {
    header("Location: index.php");
  }

  include('header.php');
?>

  <div class="container-fluid">
    <nav class="navbar navbar-light bg-faded navbar-fixed-top">
       <a class="navbar-brand text-danger">Secret Diary</a>
       <div class="pull-xs-right">
         <a href='index.php?logout=1' class="btn btn-outline-danger">Logout</a>
       </div>
    </nav>
    <textarea id="diary" class="form-control">
      <?php echo $diaryContent; ?>
    </textarea>
  </div>

<?php include('footer.php'); ?>