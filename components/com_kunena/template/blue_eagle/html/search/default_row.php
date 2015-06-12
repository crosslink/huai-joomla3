<?php

defined ( '_JEXEC' ) or die ();
?>
					<table>
					
						<tbody>
							<tr>
								<td rowspan="2" valign="top" class="kprofile-left kresultauthor">
									<ul class="kpost-profile">
										<li class="kpost-username">
											<?php echo $this->message->getAuthor()->getLink() ?>
										</li>
										<li>
											<?php
											if ($this->useravatar) :
											?>

											<span class="kavatar">
											<?php echo $this->message->getAuthor()->getLink( $this->useravatar ) ?>
											</span>

											<?php
												endif;
											?>
										</li>
									</ul>
								</td>
								<td class="kmessage-left resultmsg">
									<div class="kmsgbody">
										<div class="kmsgtitle kresult-title">
											<span class="kmsgtitle">
												<?php echo $this->getTopicLink($this->topic, $this->message, $this->subjectHtml); ?>
											</span>
										</div>
										<div class="kmsgtext resultmsg">
											<?php echo $this->messageHtml ?>
										</div>
										<div class="resultcat">
											<span class="kmsgdate"> <?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->category, $this->escape($this->category->name))) ?></span>


&nbsp;&nbsp;&nbsp;&nbsp;
<span class="kmsgdate">
										<?php echo KunenaDate::getInstance($this->message->time)->toSpan() ?>
									</span>







										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
