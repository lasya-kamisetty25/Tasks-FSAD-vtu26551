package com.smartcampus.events.controller;

import com.smartcampus.events.dto.FeedbackDto;
import com.smartcampus.events.model.*;
import com.smartcampus.events.service.*;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.List;

@Controller
@RequestMapping("/student")
@RequiredArgsConstructor
public class StudentController {

    private final EventService eventService;
    private final UserService userService;
    private final RegistrationService registrationService;
    private final FeedbackService feedbackService;
    private final AnnouncementService announcementService;

    private User getCurrentUser(UserDetails userDetails) {
        return userService.findByEmail(userDetails.getUsername());
    }

    @GetMapping("/dashboard")
    public String dashboard(@AuthenticationPrincipal UserDetails userDetails, Model model) {
        User student = getCurrentUser(userDetails);
        Page<Event> events = eventService.findAllPaged(0, 6);
        List<Registration> myRegistrations = registrationService.findByStudentActive(student);
        List<Announcement> announcements = announcementService.findRecent();

        model.addAttribute("student", student);
        model.addAttribute("events", events.getContent());
        model.addAttribute("myRegistrations", myRegistrations);
        model.addAttribute("announcements", announcements);
        model.addAttribute("totalEvents", eventService.countAllEvents());
        model.addAttribute("myRegCount", myRegistrations.size());
        return "student/dashboard";
    }

    @GetMapping("/events")
    public String browseEvents(@RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "9") int size,
            @RequestParam(required = false) String keyword,
            @RequestParam(required = false) String department,
            @RequestParam(required = false) String eventType,
            @AuthenticationPrincipal UserDetails userDetails,
            Model model) {
        Department dept = null;
        if (department != null && !department.isEmpty()) {
            try {
                dept = Department.valueOf(department);
            } catch (IllegalArgumentException ignored) {
            }
        }

        Page<Event> events = eventService.searchEvents(keyword, dept, eventType, null, null, page, size);
        model.addAttribute("student", getCurrentUser(userDetails));
        model.addAttribute("events", events);
        model.addAttribute("keyword", keyword);
        model.addAttribute("department", department);
        model.addAttribute("eventType", eventType);
        model.addAttribute("departments", Department.values());
        model.addAttribute("currentPage", page);
        model.addAttribute("totalPages", events.getTotalPages());
        return "student/events";
    }

    @GetMapping("/events/{id}")
    public String viewEvent(@PathVariable Long id,
            @AuthenticationPrincipal UserDetails userDetails,
            Model model) {
        User student = getCurrentUser(userDetails);
        Event event = eventService.findById(id);
        boolean alreadyRegistered = registrationService.isStudentRegistered(student, event);
        boolean feedbackGiven = feedbackService.hasSubmittedFeedback(student, event);
        List<Feedback> feedbacks = feedbackService.findByEvent(event);
        Double avgRating = feedbackService.averageRating(event);

        model.addAttribute("student", student);
        model.addAttribute("event", event);
        model.addAttribute("alreadyRegistered", alreadyRegistered);
        model.addAttribute("feedbackGiven", feedbackGiven);
        model.addAttribute("feedbacks", feedbacks);
        model.addAttribute("avgRating", avgRating);
        model.addAttribute("feedbackDto", new FeedbackDto());
        return "student/event-detail";
    }

    @PostMapping("/events/{id}/register")
    public String registerForEvent(@PathVariable Long id,
            @AuthenticationPrincipal UserDetails userDetails,
            RedirectAttributes redirectAttrs) {
        User student = getCurrentUser(userDetails);
        Registration reg = registrationService.registerForEvent(student, id);
        redirectAttrs.addFlashAttribute("successMsg",
                "Registered successfully! Your ticket code: " + reg.getTicketCode());
        return "redirect:/student/events/" + id;
    }

    @GetMapping("/my-registrations")
    public String myRegistrations(@AuthenticationPrincipal UserDetails userDetails, Model model) {
        User student = getCurrentUser(userDetails);
        List<Registration> registrations = registrationService.findByStudent(student);
        model.addAttribute("student", student);
        model.addAttribute("registrations", registrations);
        return "student/my-registrations";
    }

    @PostMapping("/registrations/{regId}/cancel")
    public String cancelRegistration(@PathVariable Long regId,
            @AuthenticationPrincipal UserDetails userDetails,
            RedirectAttributes redirectAttrs) {
        User student = getCurrentUser(userDetails);
        registrationService.cancelRegistration(student, regId);
        redirectAttrs.addFlashAttribute("successMsg", "Registration cancelled successfully.");
        return "redirect:/student/my-registrations";
    }

    @PostMapping("/events/{id}/feedback")
    public String submitFeedback(@PathVariable Long id,
            @Valid @ModelAttribute("feedbackDto") FeedbackDto dto,
            BindingResult result,
            @AuthenticationPrincipal UserDetails userDetails,
            RedirectAttributes redirectAttrs,
            Model model) {
        User student = getCurrentUser(userDetails);
        if (result.hasErrors()) {
            redirectAttrs.addFlashAttribute("feedbackError", "Please provide a valid rating (1-5).");
            return "redirect:/student/events/" + id;
        }
        Event event = eventService.findById(id);
        feedbackService.submitFeedback(student, event, dto);
        redirectAttrs.addFlashAttribute("successMsg", "Feedback submitted successfully. Thank you!");
        return "redirect:/student/events/" + id;
    }
}
