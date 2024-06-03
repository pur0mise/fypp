<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Simple Website</title>
    <link rel="stylesheet" href="issuer.css"> <!-- Link to your CSS file -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="page-container">
        <div id="black-box">
            <header>
                <div class="header-content">
                    <a href="adminhome.html">
                        <img src="../images/Daco_2767433.png" alt="back icon" class="back-icon">
                    </a>
                    <div class="text-container">
                        <h1>Issuer Details</h1>
                    </div>
                </div>
            </header>
            <div class="square">
                <form class="issuer-form" method="POST" action="doIssuer.php">
                    <label for="name" class="name-label">Name:</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="email" class="email-label">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="password" class="password-label">Password:</label>
                    <input type="password" id="password" name="password" required>
                    
                    <label for="nric" class="nric-label">NRIC (Last 4 Digits e.g. 1234A):</label>
                    <input type="text" id="nric" name="nric" pattern="\d{4}[A-Z]" required>

                    <div class="checkbox-container">
                        <input type="checkbox" id="school-purpose" name="school-purpose" class="big-checkbox">
                        <label for="school-purpose" class="school-purpose-label">Using this for school purposes</label>
                    </div>

                    <div id="school-fields" class="hidden">
                        <label for="polytechnic" class="polytechnic-label">Polytechnic:</label>
                        <?php
                            include('fetch_data.php');
                            $polytechnics = mysqli_query($conn, "SELECT DISTINCT Polytechnic_name FROM polytechnics");
                        ?>
                        <select id="polytechnic" name="polytechnic">
                            <option value="">Select Polytechnic</option>
                            <?php
                                while($p = mysqli_fetch_array($polytechnics)) {
                                    echo '<option value="'.htmlspecialchars($p['Polytechnic_name']).'">'.htmlspecialchars($p['Polytechnic_name']).'</option>';
                                }
                            ?>
                        </select>
                        <div id="school-diploma-container" class="field-container">
                        <div class="label-row">
                            <label for="school" class="school-label">School:</label>
                            <label for="diploma" class="diploma-label">Diploma:</label>
                        </div>
                        <div class="input-row">
                        <select id="school" name="school" class="school-input">
                        <option value="">Select School</option>
                        </select>
                        <select id="diploma" name="diploma" class="diploma-input">
                        <option value="">Select Diploma</option>
                        </select>
                    </div>
                </div>
                    </div>
                    <div class="button-container">
                        <button type="button" id="discard">DISCARD</button>
                        <button type="submit" id="submit-button" disabled>DONE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Box -->
    <div id="modal-box" class="hidden">
        <div class="modal-content">
            <h3 id="modal-header" class="modal-header">Discard Changes?</h3>
            <p id="modal-text" class="modal-text">You will lose all changes made to this issuer.</p>
            <button id="confirm-discard">Discard Changes</button>
            <button id="cancel-discard">Keep Editing</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const schoolPurposeCheckbox = document.getElementById('school-purpose');
        const schoolFields = document.getElementById('school-fields');
        let schoolInputs = document.querySelectorAll('.school-input');
        let inputFields = document.querySelectorAll('.issuer-form input, .issuer-form select');
        const submitButton = document.getElementById('submit-button');
        const discardButton = document.getElementById('discard');
        const modalBox = document.getElementById('modal-box');
        const pageContainer = document.getElementById('page-container');
        const confirmDiscard = document.getElementById('confirm-discard');
        const cancelDiscard = document.getElementById('cancel-discard');

        function updateInputFields() {
            schoolInputs = document.querySelectorAll('.school-input');
            inputFields = document.querySelectorAll('.issuer-form input, .issuer-form select');
        }

        function checkFormCompletion() {
            let allFilled = true;
            inputFields.forEach(field => {
                if (field.value.trim() === '' && field.offsetParent !== null) {
                    allFilled = false;
                }
            });

            if (schoolPurposeCheckbox.checked) {
                schoolInputs.forEach(field => {
                    if (field.value.trim() === '') {
                        allFilled = false;
                    }
                });
            }

            if (allFilled) {
                submitButton.removeAttribute('disabled');
            } else {
                submitButton.setAttribute('disabled', true);
            }
        }

        schoolPurposeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                schoolFields.classList.remove('hidden');
                schoolInputs.forEach(field => {
                    field.setAttribute('required', true);
                });
            } else {
                schoolFields.classList.add('hidden');
                schoolInputs.forEach(field => {
                    field.removeAttribute('required');
                    field.value = '';
                });
            }
            updateInputFields();
            checkFormCompletion();
        });

        inputFields.forEach(field => {
            field.addEventListener('input', checkFormCompletion);
        });

        $(document).ready(function(){
            $('#polytechnic').change(function(){
                var Stdid = $(this).val();
                
                $.ajax({
                    type: 'POST',
                    url: 'fetch_schools.php',
                    data: {id: Stdid},
                    success: function(data)
                    {
                        $('#school').html(data);
                        updateInputFields();
                        schoolInputs.forEach(field => {
                            field.addEventListener('input', checkFormCompletion);
                        });
                        checkFormCompletion();
                    }
                });
            });

            $('#school').change(function(){
                var Stdid2 = $(this).val();

                $.ajax({
                    type: 'POST',
                    url: 'fetch_diplomas.php',
                    data: {id2: Stdid2},
                    success: function(data)
                    {
                        $('#diploma').html(data);
                        updateInputFields();
                        schoolInputs.forEach(field => {
                            field.addEventListener('input', checkFormCompletion);
                        });
                        checkFormCompletion();
                    }
                });
            });
        });

        discardButton.addEventListener('click', function() {
            pageContainer.classList.add('blur');
            modalBox.classList.remove('hidden');
        });

        confirmDiscard.addEventListener('click', function() {
            // Reset form or navigate away
            window.location.href = 'adminhome.html'; // Example action, adjust as needed
        });

        cancelDiscard.addEventListener('click', function() {
            pageContainer.classList.remove('blur');
            modalBox.classList.add('hidden');
        });

        checkFormCompletion();
    </script>
</body>
</html>
