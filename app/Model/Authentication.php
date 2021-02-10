<?php
namespace App\Model;
use Nette;
use Nette\Security\SimpleIdentity;
use Nette\Database\Context;
use Nette\Database\Explorer;

class Authentication implements Nette\Security\Authenticator
{
	private $database;
	private $passwords;

	public function __construct(
		Nette\Database\Explorer $database,
		Nette\Security\Passwords $passwords
	) {
		$this->database = $database;
		$this->passwords = $passwords;
	}

	public function authenticate(string $username, string $password): SimpleIdentity
	{

		$row = $this->database->table('users')
			->where('username', $username)
			->fetch();
		$pass =  $this->passwords->hash($row->password); // Zahashuje heslo
		if (!$row) {
			throw new Nette\Security\AuthenticationException('');
		}

		if (!$this->passwords->verify($password, $pass)) {
			throw new Nette\Security\AuthenticationException("Invalid password.{$password}");
		}

		return new SimpleIdentity(
			
			$row->id,
			$row->role, // nebo pole více rolí
			['name' => $row->username]
		
		);
	}

	public function Hashovat(string $password){
		return $this->passwords->hash($password);

	}

}
?>