<?php

function cyberxdc_support_page()
{
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Save the submitted support message
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);

        // Save the message to the database (you need to implement this part)
        // Example: save_support_message($name, $email, $subject, $message);

        // Display a success message
        $notice = 'Your support message has been submitted successfully.';
    }

    // Get support messages from the database (you need to implement this part)
    // Example: $support_messages = get_support_messages();

    // Display plugin developer details (you need to implement this part)
    // Example: $developer_details = get_developer_details();
?>
    <div class="container">
        <div style="max-width: 100%; width: 100%;" class="card">
            <h3>CyberXDC Support</h3>
            <?php if (!empty($notice)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $notice; ?></p>
                </div>
            <?php endif; ?>
            <hr>
            <h4>Plugin Developer Details</h4>
            <!-- Display plugin developer details here -->
            <!-- Example: <?php echo $developer_details; ?> -->

            <h4>Support Messages</h4>
            <!-- Display support messages here -->
            <!-- Example: -->
            <?php if (!empty($support_messages)) : ?>
                <ul>
                    <?php foreach ($support_messages as $message) : ?>
                        <li>
                            <strong><?php echo $message['name']; ?></strong> (<?php echo $message['email']; ?>): <?php echo $message['message']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No support messages available.</p>
            <?php endif; ?>

            <hr>
            <h4>Submit a Support Message</h4>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="name">Name:</label></th>
                        <td><input type="text" id="name" name="name" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="email">Email:</label></th>
                        <td><input type="email" id="email" name="email" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="subject">Subject:</label></th>
                        <td><input type="text" id="subject" name="subject" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="message">Message:</label></th>
                        <td><textarea id="message" name="message" required></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="submit">Submit Message</label></th>
                        <td><input class="button" type="submit" class="button-primary" value="Submit"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<?php
}
