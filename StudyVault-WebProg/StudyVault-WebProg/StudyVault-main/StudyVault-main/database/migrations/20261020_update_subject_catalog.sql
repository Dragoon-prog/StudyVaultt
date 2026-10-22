USE studyvault;

-- Add the third-semester options to an existing database.
INSERT INTO semesters (name)
SELECT catalog.name
FROM (
    SELECT '1st Year - 3rd Sem' AS name
    UNION ALL SELECT '2nd Year - 3rd Sem'
    UNION ALL SELECT '3rd Year - 3rd Sem'
    UNION ALL SELECT '4th Year - 3rd Sem'
) AS catalog
WHERE NOT EXISTS (
    SELECT 1 FROM semesters AS existing WHERE existing.name = catalog.name
);

-- Allow multiple subjects to share a course code within the same semester.
ALTER TABLE subjects
    DROP INDEX uq_subject,
    ADD UNIQUE KEY uq_subject (semester_id, course_code, title);

-- Move the existing Web Programming record to its corrected semester.
UPDATE subjects AS s
JOIN semesters AS old_sem ON old_sem.semester_id = s.semester_id
JOIN semesters AS new_sem ON new_sem.name = '3rd Year - 1st Sem'
SET s.semester_id = new_sem.semester_id,
    s.course_code = 'BSIS241A',
    s.title = 'Web Programming'
WHERE old_sem.name = '2nd Year - 1st Sem'
  AND s.course_code = 'BSIS241A'
  AND s.title IN ('We Programming', 'Web Programming');

-- Add the complete 3rd Year, 1st Sem subject list if an entry is missing.
INSERT INTO subjects (semester_id, course_code, title)
SELECT sem.semester_id, catalog.course_code, catalog.title
FROM semesters AS sem
CROSS JOIN (
    SELECT 'BSIS241A' AS course_code, 'Financial Management' AS title
    UNION ALL SELECT 'BSIS241A', 'Evaluation of Business Performance'
    UNION ALL SELECT 'BSIS241A', 'Multimedia and Computer Graphics'
    UNION ALL SELECT 'BSIS241A', 'Web Programming'
    UNION ALL SELECT 'GENED16B', 'Purposive Communication'
) AS catalog
WHERE sem.name = '3rd Year - 1st Sem'
  AND NOT EXISTS (
      SELECT 1
      FROM subjects AS existing
      WHERE existing.semester_id = sem.semester_id
        AND existing.course_code = catalog.course_code
        AND existing.title = catalog.title
  );
