<?php

namespace orm;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 **/
class User implements \interfaces\Restable
{
    /**
     * @var int
	 * @Id 
	 * @Column(type="integer") 
	 * @GeneratedValue
     */
    protected $id;
    
	/**
     * @var string
	 * @Column(type="string", unique=true)
     */
    protected $login;
	
	/**
     * @var string
	 * @Column(type="string")
     */
    protected $password;
	
	/**
	 *
	 * @var ArrayCollection
     * @OneToMany(targetEntity="Session", mappedBy="user")
     */
	protected $sessions;
	
	public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }
	
	public function addSession(Session $session)
    {
        $session->setUser($this);
    }

    public function assignedToSession(Session $session)
    {
        $this->sessions->add($session);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }
	
	public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
	
	public function getSessions() : array
    {
        return $this->sessions;
    }

	public function toResult(): array {
		return [
			'id' => $this->getId(),
			'login' => $this->getLogin(),
			'password' => $this->getPassword()
		];
	}
}