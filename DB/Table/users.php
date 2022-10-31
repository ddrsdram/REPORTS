<?php

namespace DB\Table;


use \DB\Connection;

class users extends Connection
{
	const ORG =  'ORG';

	const id =  'id';

	const session_id =  'session_id';

	const login =  'login';

	const password =  'password';

	const password_crypto =  'password_crypto';

	const mPhone =  'mPhone';

	const fam =  'fam';

	const im =  'im';

	const ot =  'ot';

	const id_month =  'id_month';

	const id_month_this =  'id_month_this';

	const id_month_payment =  'id_month_payment';

	const admin =  'admin';

	const f_filter_JEU =  'f_filter_JEU';

	const region =  'region';

	const POS_Terminal =  'POS_Terminal';

	const POS_printOnly =  'POS_printOnly';

	const POS_printAddress =  'POS_printAddress';

	const superUser =  'superUser';

	const f_filter_typeLS =  'f_filter_typeLS';

	const edit_typeLS =  'edit_typeLS';

	const id_fiscalRegistrar =  'id_fiscalRegistrar';

	const server_fiscalRegistrar =  'server_fiscalRegistrar';

}
