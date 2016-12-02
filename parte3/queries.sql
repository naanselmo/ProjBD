-- a) Quais os espaços com postos que nunca foram alugados?

SELECT DISTINCT p.morada,
                p.codigo_espaco
FROM   posto p
       LEFT OUTER JOIN aluga a
                    ON p.morada = a.morada
                       AND p.codigo = a.codigo
WHERE  a.numero IS NULL;

-- b) Quais edifícios com um número de reservas superior à média?

SELECT morada
FROM   aluga
GROUP  BY morada
HAVING Count(*) > ( (SELECT Count(*)
                     FROM   aluga) / (SELECT Count(*)
                                      FROM   edificio) );

-- c) Quais utilizadores cujos alugáveis foram fiscalizados sempre pelo mesmo fiscal?

SELECT nif
FROM   fiscaliza
       NATURAL JOIN arrenda
GROUP  BY nif
HAVING Count(DISTINCT id) = 1;

-- d) Qual o montante total realizado (pago) por cada espaço durante o ano de 2016? Assuma que a tarifa indicada na oferta é diária. Deve considerar os casos em que o espaço foi alugado totalmente ou por postos.

SELECT morada,
       codigo,
       Sum(montante)
FROM   ((SELECT morada,
                codigo_espaco                                    AS codigo,
                ( Datediff(data_fim, data_inicio) + 1 ) * tarifa AS montante
         FROM   aluga
                NATURAL JOIN oferta
                NATURAL JOIN posto
                NATURAL JOIN paga
         WHERE  Year(data) = 2016)
        UNION
        (SELECT morada,
                codigo,
                ( Datediff(data_fim, data_inicio) + 1 ) * tarifa AS montante
         FROM   aluga
                NATURAL JOIN oferta
                NATURAL JOIN espaco
                NATURAL JOIN paga
         WHERE  Year(data) = 2016)) t
GROUP  BY morada,
          codigo;

-- e) Quais os espaços de trabalho cujos postos nele contidos foram todos alugados? (Por alugado entende-se um posto de trabalho que tenha pelo menos uma oferta aceite, independentemente das suas datas.)

SELECT morada,
       codigo_espaco
FROM   (SELECT morada,
               codigo_espaco,
               Count(*) AS count
        FROM   posto
        GROUP  BY morada,
                  codigo_espaco) r1
       NATURAL JOIN (SELECT morada,
                            codigo_espaco,
                            Count(*) AS count
                     FROM   (SELECT morada,
                                    codigo_espaco
                             FROM   posto
                                    NATURAL JOIN aluga
                                    NATURAL JOIN estado
                             WHERE  estado = 'Aceite') p
                     GROUP  BY morada,
                               codigo_espaco) r2;

-- x) Qual o montante total realizado (pago) por um dado espaço?

SELECT morada,
       codigo,
       Sum(montante)
FROM   ((SELECT morada,
                codigo_espaco                                    AS codigo,
                ( Datediff(data_fim, data_inicio) + 1 ) * tarifa AS montante
         FROM   aluga
                NATURAL JOIN oferta
                NATURAL JOIN posto
                NATURAL JOIN paga
         WHERE  codigo_espaco = 'Central'
                AND morada = 'ISEL')
        UNION
        (SELECT morada,
                codigo,
                ( Datediff(data_fim, data_inicio) + 1 ) * tarifa AS montante
         FROM   aluga
                NATURAL JOIN oferta
                NATURAL JOIN espaco
                NATURAL JOIN paga
         WHERE  codigo = 'Central'
                AND morada = 'ISEL')) t
GROUP  BY morada,
          codigo;

-- y) Qual o montante total realizado (pago) por cada espaço do utilizador de nif '143856248'?

SELECT morada,
       codigo,
       Sum(montante)
FROM   ((SELECT morada,
                codigo_espaco                            AS codigo,
                ( Datediff(data_fim, data_inicio) + 1 ) * tarifa AS montante
         FROM   aluga
                NATURAL JOIN oferta
                NATURAL JOIN posto
                NATURAL JOIN paga
                NATURAL JOIN (SELECT morada,
                                     codigo
                              FROM   arrenda
                              WHERE  nif = '113056729') u1)
        UNION
        (SELECT morada,
                codigo,
                ( Datediff(data_fim, data_inicio) + 1 ) * tarifa AS montante
         FROM   aluga
                NATURAL JOIN oferta
                NATURAL JOIN espaco
                NATURAL JOIN paga
                NATURAL JOIN (SELECT morada,
                                     codigo
                              FROM   arrenda
                              WHERE  nif = '113056729') u2)) t
GROUP  BY morada,
          codigo;

-- z) Quais as ofertas que não têm reservas associadas cujos estados sejam Paga ou Aceite?

SELECT o.morada,
       o.codigo,
       o.data_inicio,
       o.data_fim,
       o.tarifa
FROM   oferta o
       LEFT OUTER JOIN (SELECT morada,
                               codigo
                        FROM   aluga
                               NATURAL JOIN (SELECT numero
                                             FROM   estado e
                                                    NATURAL JOIN (SELECT
                               numero,
                               Max(time_stamp) AS time_stamp
                                             FROM   estado
                                             GROUP  BY numero) f
                                             WHERE  estado = "aceite"
                                                     OR estado = "paga") z) s
                    ON o.morada = s.morada
                       AND o.codigo = s.codigo
WHERE  s.codigo IS NULL;
