<?php

/*
 * LMS version 1.11-git
 *
 *  (C) Copyright 2001-2017 LMS Developers
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 */

$this->BeginTrans();

$this->Execute("DROP VIEW customerview, contractorview, customeraddressview");

$this->Execute("
	CREATE VIEW customerview AS
	    SELECT c.*,
	        a1.country_id as countryid, a1.zip as zip, a1.city as city,
	        a1.street as street,a1.house as building, a1.flat as apartment,
	        a2.country_id as post_countryid, a2.zip as post_zip,
	        a2.city as post_city, a2.street as post_street, a2.name as post_name,
	        a2.house as post_building, a2.flat as post_apartment,
	        a1.address as address, a1.location AS full_address,
	        a2.address as post_address, a2.location AS post_full_address
	    FROM customers c
	        JOIN customer_addresses ca1 ON c.id = ca1.customer_id AND ca1.type = 1
	        LEFT JOIN vaddresses a1 ON ca1.address_id = a1.id
	        LEFT JOIN customer_addresses ca2 ON c.id = ca2.customer_id AND ca2.type = 0
	        LEFT JOIN vaddresses a2 ON ca2.address_id = a2.id
	    WHERE NOT EXISTS (
	        SELECT 1 FROM customerassignments a
	        JOIN excludedgroups e ON (a.customergroupid = e.customergroupid)
	        WHERE e.userid = lms_current_user() AND a.customerid = c.id)
	        AND c.type < 2
");
$this->Execute("
	CREATE VIEW contractorview AS
	    SELECT c.*,
	        a1.country_id as countryid, a1.zip as zip, a1.city as city, a1.street as street,
	        a1.house as building, a1.flat as apartment, a2.country_id as post_countryid, 
	        a2.zip as post_zip, a2.city as post_city, a2.street as post_street, 
	        a2.house as post_building, a2.flat as post_apartment, a2.name as post_name,
	        a1.address as address, a1.location AS full_address,
	        a2.address as post_address, a2.location AS post_full_address
	    FROM customers c
	        JOIN customer_addresses ca1 ON c.id = ca1.customer_id AND ca1.type = 1
	        LEFT JOIN vaddresses a1 ON ca1.address_id = a1.id
	        LEFT JOIN customer_addresses ca2 ON c.id = ca2.customer_id AND ca2.type = 0
	        LEFT JOIN vaddresses a2 ON ca2.address_id = a2.id
	    WHERE c.type = 2
");
$this->Execute("
	CREATE VIEW customeraddressview AS
	    SELECT c.*,
	        a1.country_id as countryid, a1.zip as zip, a1.city as city, a1.street as street,
	        a1.house as building, a1.flat as apartment, a2.country_id as post_countryid,
	        a2.zip as post_zip, a1.city as post_city, a2.street as post_street,
	        a2.house as post_building, a2.flat as post_apartment, a2.name as post_name,
	        a1.address as address, a1.location AS full_address,
	        a2.address as post_address, a2.location AS post_full_address
	    FROM customers c
	        JOIN customer_addresses ca1 ON c.id = ca1.customer_id AND ca1.type = 1
	        LEFT JOIN vaddresses a1 ON ca1.address_id = a1.id
	        LEFT JOIN customer_addresses ca2 ON c.id = ca2.customer_id AND ca2.type = 0
	        LEFT JOIN vaddresses a2 ON ca2.address_id = a2.id
	    WHERE c.type < 2
");

$this->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2017041800', 'dbversion'));

$this->CommitTrans();

?>
