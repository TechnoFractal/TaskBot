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

/**
 * Description of Requestor
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 * @Entity 
 * @Table(
 *	name="requesters",
 *	uniqueConstraints={
 *		@UniqueConstraint(columns={"category_id", "tele_id"})
 *	}
 * )
 */
class Requester implements Restable
{	
	/**
     * @var int
	 * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

	/**
     * @var int
	 * @Column(type="integer")
     */
    protected $tele_id;
	
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
	 * @var \DateTime
     * @Column(type="datetime")
     **/
    protected $access_date;
	
	/**
     * @var Post
	 * @ManyToOne(targetEntity="Post", cascade={"remove"})
     **/
    protected $post;
	
	/**
     * @var Category
	 * @ManyToOne(targetEntity="Category")
     **/
    protected $category;
	
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

	public function getDate() : \DateTime
	{
		return $this->access_date;
	}
	
	public function setDate(\DateTime $date)
	{
		$this->access_date = $date;
	}
	
	public function getPost() : Post
	{
		return $this->post;
	}
	
	public function setPost(Post $post)
	{
		$this->post = $post;
	}
	
	public function getCategory() : Category
	{
		return $this->category;
	}
	
	public function setCategory(Category $category)
	{
		$this->category = $category;
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
			'accessDate' => $this->getDate()->format("Y-m-d"),
			'categoryId' => $this->getCategory()->getId(),
			'postId' => $this->getPost()->getId()
		];
	}
}
