<?php

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
	 * @Column(type="string")
     */
    protected $name;
	
	/**
     * @var string
	 * @Column(type="string")
     */
    protected $password;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
	
	public function getPassword()
    {
        return $this->name;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}