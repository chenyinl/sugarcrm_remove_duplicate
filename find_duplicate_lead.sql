SELECT 
	temp.count_,
	temp.emailadd
FROM(
	SELECT
		l.id AS lad,
		count(eaddrbean.email_address_id) AS count_, 
		eaddrbean.email_address_id ,
		eaddr.email_address AS emailadd, 
		eaddrbean.bean_id
		
	FROM 
		email_addr_bean_rel AS eaddrbean,
		email_addresses as eaddr,
		leads as l
	WHERE
		eaddr.id = eaddrbean.email_address_id AND
		l.id = eaddrbean.bean_id AND
		l.deleted=0
	GROUP BY email_address_id
) AS temp
WHERE temp.count_!=1;
