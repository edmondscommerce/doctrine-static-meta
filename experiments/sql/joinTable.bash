#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"


#number of rows:
num=${1:-1000}

repeats=${2:-10}

rowsPerQuery=${3:-1000}

echo "starting with $num rows and $repeats repeats"

#database name
dbName="dsm_exp_jointable"

mysql -e "

DROP DATABASE IF EXISTS $dbName;

CREATE DATABASE $dbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci

";


###########
mysql $dbName -e "
--
-- Table structure for table address
--

CREATE TABLE address (
  id int(11) NOT NULL,
  name varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table addresses_to_companies
--

CREATE TABLE addresses_to_companies (
  company_id int(11) NOT NULL,
  address_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table company
--

CREATE TABLE company (
  id int(11) NOT NULL,
  name varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table address
--
ALTER TABLE address
  ADD PRIMARY KEY (id);

--
-- Indexes for table addresses_to_companies
--
ALTER TABLE addresses_to_companies
  ADD PRIMARY KEY (company_id,address_id),
  ADD UNIQUE KEY UNIQ_225DBB08979B1AD6 (company_id),
  ADD KEY IDX_225DBB08F5B7AF75 (address_id);

--
-- Indexes for table company
--
ALTER TABLE company
  ADD PRIMARY KEY (id);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table address
--
ALTER TABLE address
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table company
--
ALTER TABLE company
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table addresses_to_companies
--
ALTER TABLE addresses_to_companies
  ADD CONSTRAINT FK_225DBB08F5B7AF75 FOREIGN KEY (address_id) REFERENCES company (id),
  ADD CONSTRAINT FK_225DBB08979B1AD6 FOREIGN KEY (company_id) REFERENCES address (id);

"

echo "

created schema

now inserting rows...

"

companyValues='';
addressValues='';
addressToCompaniesValues='';

function insertRows(){
    if [[ "$addressValues" == "" ]]
    then
        return 0
    fi
    echo "

    insert into address (id, name) VALUES ${addressValues##,};

    insert into company (id, name) VALUES ${companyValues##,};

    insert into addresses_to_companies (address_id, company_id) VALUES  ${addressToCompaniesValues##,};

    -- select ROW_COUNT();

    " | mysql $dbName
    echo "added $i rows"
    addressValues='';
    companyValues='';
    addressToCompaniesValues='';
}

start=1
for (( i=$start; i<=$num; i++ ))
do
    companyValues="${companyValues}, ($i, 'companyName${i}')"
    addressValues="${addressValues}, ($i, 'addressName${i}')"
    addressToCompaniesValues="${addressToCompaniesValues}, ($i, $i)"
    if (( $i % $rowsPerQuery == 0 ))
    then
        insertRows;
    fi
done
insertRows






time \
for (( i=1; i<=$repeats; i++ )) \
do
    echo "query $i"
    mysql $dbName -e "select * from address a join addresses_to_companies j on (a.id = j.address_id) join company c on (c.id=j.company_id and j.address_id=a.id)" | wc -l; \
done

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
