from pathlib import Path
from playwright.sync_api import sync_playwright
import json, os
password=os.environ['JOOMLA_TEST_ADMIN_PASSWORD']
username=os.environ.get('JOOMLA_TEST_ADMIN_USERNAME','reviewadmin')
matrix=json.loads(os.environ.get('JOOMLA_TEST_MATRIX','[["6.1.1",8111],["6.1.4",8114],["6.2.0",8120]]'))
results=[]
with sync_playwright() as p:
 browser=p.chromium.launch(executable_path='/usr/bin/chromium',headless=True,args=['--no-sandbox'])
 for version,port in matrix:
  page=browser.new_page()
  errors=[]
  page.on('pageerror',lambda e:errors.append(str(e)))
  url=f'http://127.0.0.1:{port}'
  assert page.goto(url+'/').status==200
  for layout in ['default','grid','list','media','featured','magazine','carousel']:
   assert page.locator('.jt-articlex-pro-'+layout).count()==1,layout
   assert page.locator('.jt-articlex-pro-'+layout).get_by_text('ArticleX Smoke 1',exact=True).count()>=1,layout
  carousel=page.locator('.jt-articlex-pro-carousel')
  first=carousel.locator('.carousel-item.active')
  first_text=first.inner_text()
  assert 'ArticleX Smoke' in first_text
  carousel.locator('[data-bs-slide="next"]').click()
  page.wait_for_timeout(800)
  assert carousel.locator('.carousel-item.active').inner_text()!=first_text
  assert page.request.get(url+'/media/mod_jtarticlexpro/css/site.css').status==200
  schemas=page.locator('script[type="application/ld+json"]').all_text_contents()
  assert sum(json.loads(s).get('@type')=='ItemList' for s in schemas)==7
  page.goto(url+'/administrator/')
  page.locator('#mod-login-username').fill(username)
  page.locator('#mod-login-password').fill(password)
  page.locator('button[type="submit"]').click()
  page.wait_for_url('**/administrator/index.php')
  page.wait_for_load_state('networkidle')
  hide=page.get_by_role('button',name='Hide Forever',exact=True)
  try:
   hide.wait_for(state='visible',timeout=6000)
   hide.click()
  except Exception:
   pass
  page.goto(url+'/administrator/index.php?option=com_modules&view=modules&client_id=0')
  page.wait_for_load_state('networkidle')
  page.get_by_role('link',name='ArticleX Test grid',exact=True).click(timeout=5000)
  page.wait_for_load_state('networkidle')
  assert page.locator('#jform_title').input_value()=='ArticleX Test grid'
  assert page.locator('#jform_params_layout').count()==1
  assert not errors,errors
  results.append({'version':version,'layouts':7,'carousel_next':'passed','json_ld':7,'css_http':200,'atum_module_form':'passed','js_errors':errors})
  page.close()
 browser.close()
if os.environ.get('JOOMLA_TEST_RESULT_FILE'):
 Path(os.environ['JOOMLA_TEST_RESULT_FILE']).write_text(json.dumps(results,indent=2))
print(json.dumps(results,indent=2))
