SELECT
  local_id,
  date_id,
  avg(total_pago)
FROM proj_dw.reserva
GROUP BY local_id, date_id WITH ROLLUP
UNION ALL
SELECT
  NULL AS local_id,
  date_id,
  avg(total_pago)
FROM proj_dw.reserva
GROUP BY date_id;