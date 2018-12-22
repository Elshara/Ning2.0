<?php if (mb_strlen($this->description)) { ?>
								<div class="xg_module xg_module_network_description">
									<div class="xg_module_head notitle"></div>
									<div class="xg_module_body">
										<p style="font-size:1.1em">
                                            <%= xnhtmlentities($this->description) %>
										</p>
									</div>
								</div>
<?php } ?>