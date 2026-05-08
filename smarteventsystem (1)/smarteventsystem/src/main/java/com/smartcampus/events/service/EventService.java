package com.smartcampus.events.service;

import com.smartcampus.events.dto.EventDto;
import com.smartcampus.events.exception.ResourceNotFoundException;
import com.smartcampus.events.model.Department;
import com.smartcampus.events.model.Event;
import com.smartcampus.events.model.User;
import com.smartcampus.events.repository.EventRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.*;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.List;

@Service
@RequiredArgsConstructor
public class EventService {

    private final EventRepository eventRepository;

    public List<Event> findAll() {
        return eventRepository.findAll(Sort.by(Sort.Direction.DESC, "createdAt"));
    }

    public Page<Event> findAllPaged(int page, int size) {
        return eventRepository.findAll(PageRequest.of(page, size, Sort.by(Sort.Direction.DESC, "startDate")));
    }

    public Page<Event> searchEvents(String keyword, Department department, String eventType,
            LocalDate startDate, LocalDate endDate, int page, int size) {
        Pageable pageable = PageRequest.of(page, size, Sort.by(Sort.Direction.DESC, "startDate"));
        return eventRepository.searchEvents(keyword, department, eventType, startDate, endDate, pageable);
    }

    public Event findById(Long id) {
        return eventRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Event", id));
    }

    @Transactional
    public Event createEvent(EventDto dto, User admin) {
        Event event = Event.builder()
                .title(dto.getTitle())
                .description(dto.getDescription())
                .department(dto.getDepartment())
                .eventType(dto.getEventType())
                .venue(dto.getVenue())
                .startDate(dto.getStartDate())
                .endDate(dto.getEndDate())
                .lastDateToApply(dto.getLastDateToApply())
                .seatLimit(dto.getSeatLimit())
                .registeredCount(0)
                .createdBy(admin)
                .build();
        return eventRepository.save(event);
    }

    @Transactional
    public Event updateEvent(Long id, EventDto dto) {
        Event event = findById(id);
        event.setTitle(dto.getTitle());
        event.setDescription(dto.getDescription());
        event.setDepartment(dto.getDepartment());
        event.setEventType(dto.getEventType());
        event.setVenue(dto.getVenue());
        event.setStartDate(dto.getStartDate());
        event.setEndDate(dto.getEndDate());
        event.setLastDateToApply(dto.getLastDateToApply());
        event.setSeatLimit(dto.getSeatLimit());
        return eventRepository.save(event);
    }

    @Transactional
    public void deleteEvent(Long id) {
        Event event = findById(id);
        eventRepository.delete(event);
    }

    @Transactional
    public void incrementRegistrationCount(Event event) {
        event.setRegisteredCount(event.getRegisteredCount() + 1);
        eventRepository.save(event);
    }

    @Transactional
    public void decrementRegistrationCount(Event event) {
        if (event.getRegisteredCount() > 0) {
            event.setRegisteredCount(event.getRegisteredCount() - 1);
            eventRepository.save(event);
        }
    }

    public long countAllEvents() {
        return eventRepository.count();
    }

    public List<Object[]> countEventsByDepartment() {
        return eventRepository.countEventsByDepartment();
    }

    public List<Object[]> countEventsByType() {
        return eventRepository.countEventsByType();
    }

    public EventDto toDto(Event event) {
        EventDto dto = new EventDto();
        dto.setTitle(event.getTitle());
        dto.setDescription(event.getDescription());
        dto.setDepartment(event.getDepartment());
        dto.setEventType(event.getEventType());
        dto.setVenue(event.getVenue());
        dto.setStartDate(event.getStartDate());
        dto.setEndDate(event.getEndDate());
        dto.setLastDateToApply(event.getLastDateToApply());
        dto.setSeatLimit(event.getSeatLimit());
        return dto;
    }
}
