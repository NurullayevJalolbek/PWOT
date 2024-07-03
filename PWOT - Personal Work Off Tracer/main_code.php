<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ish vaqti kiritish</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        p{
            color: red
        }

        </style>
</head>

<body>
    <div class="container">
        <label>
            Ish vaqti 08:00 dan<br>
            Tugashi 17:00 gacha shu oraliqda hisoblanadi
        </label><br>
        <div class="container">
            <h1> PWOT - Personal Off Tracer</h1>
        </div>

        <form action="main_code.php" method="POST">
            <div class="row g-3">
                <div class="col">
                    <input type="datetime-local" class="form-control" name="arrival_time">
                </div>
                <div class="col">
                    <input type="datetime-local" class="form-control" name="end_time">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">SUBMIT</button>
            <br>
        </form>
        <?php
        require "Read_information.php";
        if (!empty($_POST)) {
            if ($_POST['arrival_time'] != ""  &&  $_POST['end_time'] != "") {
                $ARRIVAL_TIME = $_POST['arrival_time'];
                $END_TIME = $_POST['end_time'];
                $num = new Read_information($ARRIVAL_TIME, $END_TIME);
                $ish_soati =  $num->Working_time();
                $qarz_soati =  $num->Debt();

                $pdo = new PDO(
                    $dsn = 'mysql:host=localhost;dbname=pwot',
                    $username = 'root',
                    $password = '@jalol2004'
                );

                $query = "INSERT INTO  pwot_table (arrival_time, time_to_leave, worked, debt)
                                    VALUES (:arrival_time, :time_to_leave, :worked, :debt)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':arrival_time', $ARRIVAL_TIME);
                $stmt->bindParam(':time_to_leave', $END_TIME);
                $stmt->bindParam(':worked', $ish_soati);
                $stmt->bindParam(':debt', $qarz_soati);

                $stmt->execute();

                $query = $pdo->query("SELECT * FROM pwot_table")->fetchAll();

                echo "<h3> Malumotlar Jadval ko'rinishida </h3>";
                echo '<table class="table table-dark table-striped-columns">';
                echo '<thead class="thead-dark">';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>arrival_time</th>';
                echo '<th>time_to_leave</th>';
                echo '<th>worked</th>';
                echo '<th>debt</th>';
                echo '<th>Done</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($query as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['arrival_time'] . '</td>';
                    echo '<td>' . $row['time_to_leave'] . '</td>';
                    echo '<td>' . $row['worked'] . '</td>';
                    echo '<td>' . $row['debt'] . '</td>'; 
                    echo '<td><input type="checkbox" name="selected_rows[]" value="' . $row['id'] . '"></td>';
                    echo '<td><button type="button" onclick="handleButtonClick(' . $row['id'] . ')">Done</button></td>';
                    
    
                }

                echo '</tbody>';
                echo '</table>';
                $umumiyMINUT = 0;
                foreach ($query as $row) {
                    $time_parts = explode(":", $row['debt']);
                    $umumiyMINUT += ($time_parts[0] * 60) + $time_parts[1];
                }
                $soat = floor($umumiyMINUT / 60);
                $daqiqa = $umumiyMINUT % 60; ?>

                <p class="qizil-tex">Umumiy qarzingiz:  <?php echo "$soat soat $daqiqa" ?> daqiqa</p>";
           <?php } else {
                echo "Malumotlar kiritilmagan:";
            }
        }
        ?>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>