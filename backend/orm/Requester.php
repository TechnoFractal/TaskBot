<?php

/*
 * Copyright (C) 2018 Olga Pshenichnikova <olga@technofractal.org>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace orm;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Requestor
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 * @Entity 
 * @Table(
 *	uniqueConstraints={
 *		@UniqueConstraint(columns={"tele_id"})
 *	}
 * )
 */
class Requester implements \interfaces\Restable
{	
	/**
     * @var int
	 * @Id 
	 * @Column(type="integer") 
	 * @GeneratedValue
     */
    protected $id;
	
	/**
	 * @var ArrayCollection Last fetched task
     * @OneToMany(targetEntity="Queuepointer", mappedBy="requester")
     */
	protected $queuepointers;
	
	/**
	 * @var ArrayCollection
	 * @ManyToMany(targetEntity="Post")
	 * @JoinTable(name="performed_tasks")
	 */
	protected $performed;
	
	/**
	 * @var ArrayCollection
	 * @ManyToMany(targetEntity="Post")
	 * @JoinTable(name="postponed_tasks")
	 */	
	protected $postponed;

	/**
     * @var int
	 * @Column(type="integer")
     */
    protected $tele_id;
	
	/**
     * @var int
	 * @Column(type="integer")
     */
    protected $chat_id;	
	
	/**
     * @var bool
	 * @Column(type="boolean")
     */
    protected $is_bot;
	
	/**
     * @var string
	 * @Column(type="string")
     */
    protected $first_name;
			
	/**
     * @var string
	 * @Column(type="string")
     */
    protected $last_name;
	
	/**
     * @var string
	 * @Column(type="string")
     */
    protected $username;
	
	/**
     * @var boolean
	 * @Column(type="boolean")
     */
	protected $enabled = true;
	
	public function __construct() {
		$this->queuepointers = new ArrayCollection();
		$this->performed = new ArrayCollection();
		$this->postponed = new ArrayCollection();
	}
	
	public function isEnabled() : bool
	{
		return $this->enabled;
	}
	
	public function enable()
	{
		$this->enabled = true;
	}

	public function disable()
	{
		$this->enabled = false;
	}
	
	public function done(Post $post)
	{
		if ($this->performed->contains($post))
		{
			throw new \exceptions\PostAlreadyDone($post);
		}
		
		$this->postponed->removeElement($post);
		$this->performed->add($post);
	}
	
	public function postpone(Post $post)
	{
		if ($this->performed->contains($post)) 
		{
			throw new \exceptions\PostAlreadyDone($post);
		}
		
		if ($this->postponed->contains($post))
		{
			throw new \exceptions\PostAlreadyPostponed($post);
		}
		
		$this->postponed->add($post);
	}
	
	public function havePostponed() : bool
	{
		return !$this->postponed->isEmpty();
	}
	
	public function slicePostponed() : Post
	{
		/* @var $post Post */
		$post = $this->postponed->first();
		$this->postponed->removeElement($post);
		return $post;
	}
	
	public function isLoaded() : bool
	{
		return (bool)$this->id;
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function getTeleId() : int
	{
		return $this->tele_id;
	}
	
	public function setTeleId(int $teleId)
	{
		$this->tele_id = $teleId;
	}
	
	public function getChatId() : int
	{
		return $this->chat_id;
	}
	
	public function setChatId(int $chatId)
	{
		$this->chat_id = $chatId;
	}	
	
	public function getIsBot() : bool
	{
		return $this->is_bot;
	}
	
	public function setIsBot(bool $isBot)
	{
		$this->is_bot = $isBot;
	}
	
	public function getFirstName() : string
	{
		return $this->first_name;
	}
	
	public function setFirstName(string $firstName)
	{
		$this->first_name = $firstName;
	}
	
	public function getLastName() : string
	{
		return $this->last_name;
	}
	
	public function setLastName(string $lastName)
	{
		$this->last_name = $lastName;
	}
	
	public function getUserName() : string
	{
		return $this->username;
	}
	
	public function setUserName(string $userName)
	{
		$this->username = $userName;
	}

	public function toResult(): array 
	{
		return [
			'id' => $this->getId(),
			'teleId' => $this->getTeleId(),
			'isBot' => $this->getIsBot(),
			'firstName' => $this->getFirstName(),
			'lastName' => $this->getLastName(),
			'userName' => $this->getUserName(),
			'enabled' => $this->isEnabled()
		];
	}
}
