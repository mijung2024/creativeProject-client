<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Calculator</title>
</head>
<body>
    <h1>Calculator</h1>


    <form method="GET" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
        <label for="input1">First Input Value:</label>
        <input type="number" name="input1" id="input1" step="any" required><br><br>

        <label for="input2">Second Input Value:</label>
        <input type="number" name="input2" id="input2" step="any" required><br><br>

        <h3>Operation:</h3>
        <input type="radio" name="calculator" value="addition" id="addition" required>
        <label for="addition">+</label><br>

        <input type="radio" name="calculator" value="subtraction" id="subtraction" required>
        <label for="subtraction">-</label><br>

        <input type="radio" name="calculator" value="multiplication" id="multiplication" required>
        <label for="multiplication">×</label><br>

        <input type="radio" name="calculator" value="division" id="division" required>
        <label for="division">÷</label><br><br>

        <input type="submit" value="Calculate">
    </form>


    <?php
    if (isset($_GET['input1']) && isset($_GET['input2']) && isset($_GET['calculator'])) {
        $input1 = floatval($_GET['input1']);
        $input2 = floatval($_GET['input2']);
        $operation = $_GET['calculator'];
        $ans = '';

        if ($operation == 'addition') {
            $ans = $input1 + $input2;
        } elseif ($operation == 'subtraction') {
            $ans = $input1 - $input2;
        } elseif ($operation == 'multiplication') {
            $ans = $input1 * $input2;
        } elseif ($operation == 'division') {
            if ($input2 != 0) {
                $ans = $input1 / $input2;
            } else {
                $ans = 'Division by zero is not allowed.';
            }
        } else {
            $result = 'Invalid operation.';
        }

        echo "<h3>Answer: $ans</h3>";
    }
    ?>
</body>
</html>
