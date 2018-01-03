<?php

namespace orm;

//use \Doctrine\ORM\Annotation as ORM;

/**
 * @Entity @Table(name="users")
 **/
class User
{
    /**
     * @var int
	 * @Id @Column(type="integer") @GeneratedValue
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
	 * @var Session[]
	 */
	protected $sessions;
	
	public function __construct()
    {
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	public function addSession(Session $session)
    {
        $this->sessions[] = $session;
    }

    public function assignedToSession(Session $session)
    {
        $this->sessions[] = $session;
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
}