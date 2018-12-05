CCC = clang++
LEX = flex
YACC = bison
CFLAGS = -Wall
LEXFLAGS = -Wno-unused

OBJS = main.o ast.o parse.tab.o lex.yy.o

run: $(OBJS)
	$(CCC) $(CFLAGS) -o run $(OBJS)

main.o: main.cpp parse.y scan.l parse.tab.cpp lex.yy.cpp
	$(CCC) $(CFLAGS) -c main.cpp

parse.tab.cpp: parse.y ast.h
	$(YACC) -oparse.tab.cpp -d parse.y

ast.o: ast.cpp ast.h
	$(CCC) $(CFLAGS) -c ast.cpp

parse.tab.o: parse.tab.cpp
	$(CCC) $(CFLAGS) -c parse.tab.cpp

lex.yy.cpp: scan.l parse.tab.o
	$(LEX) -olex.yy.cpp scan.l

lex.yy.o: lex.yy.cpp
	$(CCC) $(CFLAGS) $(LEXFLAGS) -c lex.yy.cpp
	
clean:
	rm -f run *.o parse.tab.cpp lex.yy.cpp
	rm -f parse.tab.h
