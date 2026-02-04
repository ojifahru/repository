-- Duplicate title detection (case-insensitive, normalized newlines)
SELECT
    normalized_title,
    COUNT(*) AS duplicate_count,
    GROUP_CONCAT(id ORDER BY id) AS ids
FROM (
    SELECT
        id,
        TRIM(REGEXP_REPLACE(LOWER(title), '[\\r\\n]+', ' ')) AS normalized_title
    FROM tri_dharmas
    WHERE status = 'published' AND deleted_at IS NULL
) AS normalized
GROUP BY normalized_title
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC, normalized_title;

-- Optional cleanup preview (show rows for a given normalized title)
-- Replace :title_norm with a normalized title string from the query above.
-- SELECT id, title, publish_year, created_at
-- FROM tri_dharmas
-- WHERE status = 'published'
--   AND deleted_at IS NULL
--   AND TRIM(REGEXP_REPLACE(LOWER(title), '[\\r\\n]+', ' ')) = :title_norm
-- ORDER BY id;
