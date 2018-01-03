<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace orm;

//use \Doctrine\ORM\Annotation as ORM;

/**
 * Description of Session
 *
 * @author olga
 * @Entity @Table(name="sessions")
 **/
class Session {
	/**
     * @var int
	 * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;
	
	/**
     * @var User
	 * @ManyToOne(targetEntity="User", inversedBy="sessions")
     **/
    protected $user;
	
	/**
	 * @var \DateTime
     * @Column(type="datetime")
     **/
    protected $created;

    /**
	 * @var string
     * @Column(type="string")
     **/
    protected $token;
	
	public function setUser(User $user)
    {
        $user->assignedToSession($this);
        $this->user = $user;
    }
	

    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    public function getCreated() : \DateTime
    {
        return $this->created;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function getToken() : string
    {
        return $this->token;
    }
}
