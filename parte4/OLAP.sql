SELECT   AVG(total_pago)
FROM     reserva
GROUP BY local_id,date_id with CUBE;