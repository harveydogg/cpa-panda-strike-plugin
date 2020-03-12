all: clean compress
clean:
	rm -f ./cpa-panda-strike-plugin.zip
compress:
	cd ../ && zip -r ./cpa-panda-strike-plugin/cpa-panda-strike-plugin.zip ./cpa-panda-strike-plugin -x *.git* -x *.DS_Store*
