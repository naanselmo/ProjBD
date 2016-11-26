SELECT p.morada,p.codigo
FROM posto p LEFT OUTER JOIN aluga a ON p.morada=a.morada AND p.codigo=a.codigo
WHERE a.morada is NULL
AND a.codigo is NULL;

SELECT morada
FROM aluga
GROUP BY morada
HAVING (SELECT COUNT(numero) FROM aluga)>= (count(numero)/count(DISTINCT morada));

SELECT nif
FROM fiscaliza NATURAL JOIN arrenda
GROUP BY NIF
HAVING count(DISTINCT id)=1;

Select e.morada,e.codigo, SUM(o.tarifa) * 365 as montanteAnual
FROM paga p NATURAL JOIN aluga a NATURAL JOIN oferta o NATURAL JOIN espaco e
WHERE p.data BETWEEN '2016-01-01' and '2016-12-31'
Group by e.morada , e.codigo;

Select codigo_espaco
FROM aluga NATURAL JOIN estado NATURAL JOIN posto
GROUP BY codigo_espaco
HAVING (Select count(codigo) as numPostos
        FROM posto
        GROUP by codigo_espaco) = (SELECT count(morada)
                                   FROM aluga NATURAL JOIN estado
                                   WHERE estado='aceite');
