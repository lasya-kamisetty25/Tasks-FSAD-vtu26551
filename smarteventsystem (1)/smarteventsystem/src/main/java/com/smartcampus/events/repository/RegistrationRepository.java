package com.smartcampus.events.repository;

import com.smartcampus.events.model.Event;
import com.smartcampus.events.model.Registration;
import com.smartcampus.events.model.RegistrationStatus;
import com.smartcampus.events.model.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface RegistrationRepository extends JpaRepository<Registration, Long> {

    List<Registration> findByStudent(User student);

    List<Registration> findByStudentAndStatus(User student, RegistrationStatus status);

    List<Registration> findByEvent(Event event);

    Optional<Registration> findByStudentAndEvent(User student, Event event);

    boolean existsByStudentAndEvent(User student, Event event);

    long countByEvent(Event event);

    long countByEventAndStatus(Event event, RegistrationStatus status);

    @Query("SELECT r.event.id, COUNT(r) FROM Registration r WHERE r.status = :status GROUP BY r.event.id")
    List<Object[]> countRegistrationsByEvent(@Param("status") RegistrationStatus status);

    @Query("SELECT r.event.department, COUNT(r) FROM Registration r WHERE r.status = :status GROUP BY r.event.department")
    List<Object[]> countRegistrationsByDepartment(@Param("status") RegistrationStatus status);

    @Query("SELECT COUNT(r) FROM Registration r WHERE r.status = :status")
    Long countTotalActiveRegistrations(@Param("status") RegistrationStatus status);

    @Query("SELECT r FROM Registration r JOIN FETCH r.event JOIN FETCH r.student WHERE r.student = :student")
    List<Registration> findByStudentWithDetails(@Param("student") User student);
}
