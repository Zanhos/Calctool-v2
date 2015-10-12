<?php

class Activity extends Eloquent {

	protected $table = 'activity';
	protected $guarded = array('id', 'priority');

	public $timestamps = false;

	public function chapter() {
		return $this->hasOne('Chapter');
	}

	public function tax() {
		return $this->hasOne('Tax', 'id', 'tax_id');
	}

}
