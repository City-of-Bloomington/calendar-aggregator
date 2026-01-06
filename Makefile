APPNAME=calendars
REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)

default: clean compile package

clean:
	rm -Rf build
	mkdir build

	rm -Rf public/css/.sass-cache

compile: $(LANGUAGES)
	cd public/css && sassc -t compact -m screen.scss screen.css

package:
	rsync -rl --exclude-from=buildignore --delete . build/$(APPNAME)
	cd build && tar czf $(APPNAME).tar.gz $(APPNAME)

$(LANGUAGES):
	cd $@ && msgfmt -cv *.po

