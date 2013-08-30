<?php

namespace Message\Mothership\Discount\Controller;

use Message\Mothership\Discount\Discount;
use Message\Cog\Controller\Controller;
use Message\Cog\ValueObject\DateTimeImmutable;

class Create extends Controller
{
	public function index()
	{
		return $this->render('::discount:create', array(
			'form'  => $this->_getForm(),
		));
	}

	public function process()
	{
		$form = $this->_getForm();
		if ($form->isValid() && $data = $form->getFilteredData()) {
			$discount = new Discount\Discount;

			$discount->code 		= $data['code'];
			$discount->name 		= $data['name'];
			$discount->description 	= $data['description'];

			$discount->authorship->create(new DateTimeImmutable, $this->get('user.current')->id);

			$discount->start = ($data['start'] !== null ? $data['start'] : null);
			$discount->end   = ($data['end']   !== null ? $data['end']   : null);

			if(!$discount->hasValidStartEnd()) {
				$this->addFlash('error', 'Start date must be before end date!');
			} else {
				$discount = $this->get('discount.create')->create($discount);

				if($discount->id) {
					$this->addFlash('success', sprintf('You successfully added discount "%s"!', $discount->name));
					return $this->redirectToRoute('ms.discount.edit', array('discountID' => $discount->id));
				}
			}
		}

		return $this->render('::discount:create', array(
			'form'  => $form,
		));
	}

	protected function _getForm()
	{
		$form = $this->get('form')
			->setName('discount-create')
			->setAction($this->generateUrl('ms.discount.create.action'))
			->setMethod('post');

		$form->add('name', 'text', 'Name')
			->val()
			->maxLength(255);

		$form->add('description', 'textarea', 'Description')
			->val()->optional();

		$form->add('code', 'text', 'Code')
			->val()->maxLength(10);

		$dateEmptyValues = array(
			'year' 	 => 'Year',
			'month'  => 'Month',
			'day' 	 => 'Day',
			'hour' 	 => 'Hour',
			'minute' => 'Minute'
		);

		$form->add('start', 'datetime', 'Start', array('empty_value' => $dateEmptyValues))
			->val()->optional();

		$form->add('end', 'datetime', 'End', array('empty_value' => $dateEmptyValues))
			->val()->optional();

		return $form;
	}
}