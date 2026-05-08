package com.smartcampus.events.dto;

import com.smartcampus.events.model.Department;
import jakarta.validation.constraints.*;
import lombok.*;

import java.time.LocalDate;

@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class EventDto {

    @NotBlank(message = "Event title is required")
    @Size(min = 3, max = 200, message = "Title must be 3–200 characters")
    private String title;

    @Size(max = 2000, message = "Description too long")
    private String description;

    @NotNull(message = "Department is required")
    private Department department;

    @NotBlank(message = "Event type is required")
    private String eventType;

    @NotBlank(message = "Venue is required")
    private String venue;

    @NotNull(message = "Start date is required")
    private LocalDate startDate;

    @NotNull(message = "End date is required")
    private LocalDate endDate;

    @NotNull(message = "Last date to apply is required")
    private LocalDate lastDateToApply;

    @NotNull(message = "Seat limit is required")
    @Min(value = 1, message = "Seat limit must be at least 1")
    private Integer seatLimit;
}
