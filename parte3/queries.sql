a) Quais os espaços com postos que nunca foram alugados?

SELECT DISTINCT p.morada,
                p.codigo_espaco
FROM   posto p
       LEFT OUTER JOIN aluga a
                    ON p.morada = a.morada
                       AND p.codigo = a.codigo
WHERE  a.numero IS NULL;

b) Quais edifícios com um número de reservas superior à média?

Calcular média alugueres/edificios
Selecionar onde numero de alugueres superior à média

SELECT morada
FROM   aluga
GROUP  BY morada
HAVING (SELECT Count(*)
        FROM   aluga) >= ( Count(*) / Count(DISTINCT morada) );

c) Quais utilizadores cujos alugáveis foram fiscalizados sempre pelo mesmo fiscal?

SELECT nif
FROM   fiscaliza
       NATURAL JOIN arrenda
GROUP  BY nif
HAVING Count(DISTINCT id) = 1;

d) Qual o montante total realizado (pago) por cada espaço durante o ano de 2016? Assuma que a tarifa indicada na oferta é diária. Deve considerar os casos em que o espaço foi alugado totalmente ou por postos.

SELECT e.morada,
       e.codigo,
       Sum(o.tarifa) * 365 AS montanteAnual
FROM   paga p
       NATURAL JOIN aluga a
       NATURAL JOIN oferta o
       NATURAL JOIN espaco e
WHERE  p.data BETWEEN '2016-01-01' AND '2016-12-31'
GROUP  BY e.morada,
          e.codigo;

e) Quais os espaços de trabalho cujos postos nele contidos foram todos alugados? (Por alugado entende-se um posto de trabalho que tenha pelo menos uma oferta aceite, independentemente das suas datas.)

SELECT codigo_espaco
FROM   aluga
       NATURAL JOIN estado
       NATURAL JOIN posto
GROUP  BY codigo_espaco
HAVING (SELECT Count(codigo) AS numPostos
        FROM   posto
        GROUP  BY codigo_espaco) = (SELECT Count(morada)
                                    FROM   aluga
                                           NATURAL JOIN estado
                                    WHERE  estado = 'aceite');

z) Quais as ofertas que não têm reservas associadas cujos estados sejam Paga ou Aceite

SELECT o.morada, o.codigo, o.data_inicio, o.data_fim, o.tarifa
FROM oferta o LEFT OUTER JOIN (
  SELECT morada, codigo
  FROM aluga NATURAL JOIN (
    SELECT numero
    FROM estado e NATURAL JOIN (
      SELECT numero, MAX(time_stamp) AS time_stamp
      FROM estado
      GROUP BY numero
    ) f
    WHERE estado = "Aceite" OR estado = "Paga"
  ) z
) s
ON o.morada = s.morada
AND o.codigo = s.codigo
WHERE s.codigo IS NULL;
