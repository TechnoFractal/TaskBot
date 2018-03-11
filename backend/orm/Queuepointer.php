<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace orm;

/**
 * Description of Queuepointer
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 * @Entity
 */
class Queuepointer 
{
	/**
	 * @var Requester
	 * @Id
	 * @ManyToOne(targetEntity="Requester", inversedBy="requesters")
     **/
    protected $requester;

	/**
     * @var Category
	 * @Id
	 * @ManyToOne(targetEntity="Category")
     **/
    protected $category;
	
	/**
     * @var Post
	 * @ManyToOne(targetEntity="Post")
	 * @JoinColumn(onDelete="cascade")
     **/
    protected $post;
	
	/**
	 * @var \DateTime
     * @Column(type="datetime")
     **/
    protected $access_date;
	
	/**
	 *
	 * @var bool
	 * @Column(type="boolean")
	 */
	protected $isLast = false;
	
	public function getId() : int
	{
		return $this->id;
	}

	public function getIsLast() : bool
	{
		return $this->isLast;
	}
	
	public function setIsLast()
	{
		$this->isLast = true;
	}
	
	public function setIsNotLast()
	{
		$this->isLast = false;
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
	
	public function getRequester() : Requester
	{
		return $this->requester;
	}
	
	public function setRequester(Requester $requester)
	{
		$this->requester = $requester;
	}
}
