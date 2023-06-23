<?php
Routes::Get("/",[controllers\home::class,"index"]);
Routes::Get("/page",[controllers\home::class,"page"]);
Routes::Get("/Body_section",[controllers\home::class,"Body_section"]);
Routes::Post("/make_pdf",[controllers\home::class,"make_pdf"]);