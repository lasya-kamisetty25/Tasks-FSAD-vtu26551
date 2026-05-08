package com.smartcampus.events.service;

import com.smartcampus.events.model.Event;
import com.smartcampus.events.model.Registration;
import com.smartcampus.events.model.User;
import jakarta.mail.internet.MimeMessage;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.mail.javamail.MimeMessageHelper;
import org.springframework.stereotype.Service;

import java.time.format.DateTimeFormatter;

@Service
@RequiredArgsConstructor
@Slf4j
public class EmailService {

    private final JavaMailSender mailSender;

    @Value("${spring.mail.username:noreply@smartcampus.edu}")
    private String fromEmail;

    /**
     * Sends a registration confirmation email.
     */
    public void sendRegistrationConfirmation(Registration registration) {
        User student = registration.getStudent();
        Event event = registration.getEvent();

        String subject = "Registration Confirmed: " + event.getTitle();
        String content = "<h3>Hello " + student.getName() + ",</h3>"
                + "<p>You have successfully registered for <strong>" + event.getTitle() + "</strong>.</p>"
                + "<p><strong>Venue:</strong> " + event.getVenue() + "<br>"
                + "<strong>Date:</strong> " + event.getStartDate().format(DateTimeFormatter.ofPattern("dd MMM yyyy"))
                + "</p>"
                + "<p>Your Ticket Code is: <strong>" + registration.getTicketCode() + "</strong></p>"
                + "<br><p>Best Regards,<br>Smart Campus Event Management</p>";

        sendEmail(student.getEmail(), subject, content);
    }

    /**
     * Sends an upcoming event reminder.
     */
    public void sendEventReminder(User student, Event event, int daysLeft) {
        String subject = "Reminder: " + event.getTitle() + " is in " + daysLeft + " days!";
        String content = "<h3>Hello " + student.getName() + ",</h3>"
                + "<p>This is a quick reminder that <strong>" + event.getTitle() + "</strong> is coming up soon on "
                + event.getStartDate().format(DateTimeFormatter.ofPattern("dd MMM yyyy")) + ".</p>"
                + "<p>Don't forget to mark your calendar!</p>"
                + "<br><p>See you there,<br>Smart Campus Event Management</p>";

        sendEmail(student.getEmail(), subject, content);
    }

    private void sendEmail(String to, String subject, String text) {
        try {
            // Attempt to send real email
            MimeMessage message = mailSender.createMimeMessage();
            MimeMessageHelper helper = new MimeMessageHelper(message, true, "UTF-8");

            helper.setFrom(fromEmail);
            helper.setTo(to);
            helper.setSubject(subject);
            helper.setText(text, true); // true indicates HTML format

            mailSender.send(message);
            log.info("Email sent successfully to: {}", to);

        } catch (Exception e) {
            // Fallback for demonstration since actual SMTP credentials might be missing
            log.warn("SMTP Connection failed or not configured. Printing email to console instead.");
            log.info(
                    "\n========== SIMULATED EMAIL ==========\nTo: {}\nSubject: {}\nContent:\n{}\n=====================================",
                    to, subject, text);
        }
    }
}
