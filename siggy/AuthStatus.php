<?php

namespace Siggy;

class AuthStatus {
	const NOACCESS = 0;
	const GROUP_SELECT_REQUIRED = 1;
	const CHAR_CORP_INVALID = 2;
	const GPASSWRONG = 5;

	const ACCEPTED = 3;

	const BLACKLISTED = 9;

	const GUEST = 8;
}