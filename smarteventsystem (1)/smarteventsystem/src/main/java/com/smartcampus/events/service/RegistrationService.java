package com.smartcampus.events.service;

import com.smartcampus.events.exception.*;
import com.smartcampus.events.model.*;
import com.smartcampus.events.repository.RegistrationRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.UUID;

@Service
@RequiredArgsConstructor
public class RegistrationService {

    private final RegistrationRepository registrationRepository;
    private final EventService eventService;
    private final EmailService emailService;

    @Transactional
    public Registration registerForEvent(User student, Long eventId) {
        Event event = eventService.findById(eventId);

        if (!event.isRegistrationOpen()) {
            throw new RegistrationClosedException(event.getTitle());
        }
        if (!event.isSeatsAvailable()) {
            throw new SeatLimitExceededException(event.getTitle());
        }
        if (registrationRepository.existsByStudentAndEvent(student, event)) {
            throw new DuplicateRegistrationException(event.getTitle());
        }

        Registration registration = Registration.builder()
                .student(student)
                .event(event)
                .ticketCode("TKT-" + UUID.randomUUID().toString().substring(0, 8).toUpperCase())
                .status(RegistrationStatus.REGISTERED)
                .build();

        eventService.incrementRegistrationCount(event);
        registration = registrationRepository.save(registration);

        emailService.sendRegistrationConfirmation(registration);

        return registration;
    }

    @Transactional
    public void cancelRegistration(User student, Long registrationId) {
        Registration reg = registrationRepository.findById(registrationId)
                .orElseThrow(() -> new ResourceNotFoundException("Registration", registrationId));

        if (!reg.getStudent().getId().equals(student.getId())) {
            throw new ResourceNotFoundException("Registration", registrationId);
        }
        if (reg.getStatus() == RegistrationStatus.CANCELLED) {
            throw new IllegalStateException("Registration is already cancelled.");
        }

        reg.setStatus(RegistrationStatus.CANCELLED);
        registrationRepository.save(reg);
        eventService.decrementRegistrationCount(reg.getEvent());
    }

    public List<Registration> findByStudent(User student) {
        return registrationRepository.findByStudentWithDetails(student);
    }

    public List<Registration> findByStudentActive(User student) {
        return registrationRepository.findByStudentAndStatus(student, RegistrationStatus.REGISTERED);
    }

    public List<Registration> findByEvent(Event event) {
        return registrationRepository.findByEvent(event);
    }

    public boolean isStudentRegistered(User student, Event event) {
        return registrationRepository.existsByStudentAndEvent(student, event);
    }

    public long countByEvent(Event event) {
        return registrationRepository.countByEventAndStatus(event, RegistrationStatus.REGISTERED);
    }

    public List<Object[]> countRegistrationsByEvent() {
        return registrationRepository.countRegistrationsByEvent(RegistrationStatus.REGISTERED);
    }

    public List<Object[]> countRegistrationsByDepartment() {
        return registrationRepository.countRegistrationsByDepartment(RegistrationStatus.REGISTERED);
    }

    public long countTotalRegistrations() {
        return registrationRepository.countTotalActiveRegistrations(RegistrationStatus.REGISTERED);
    }
}
