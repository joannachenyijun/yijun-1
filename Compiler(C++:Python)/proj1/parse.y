//  Source code for "flex & bison", by John Levine
//  Declarations for an AST calculator fb3-1
//  Adapted by Brian Malloy
%{
#include <iostream>
#include "ast.h"
   int id = 0;
  extern int yylex();
  bool type_of_number;
  void yyerror(const char *s) { std::cout << s << std::endl; }
  void yyerror(const char *s, const char ch) {
    std::cout << s << ch << std::endl;
  }
%}

%union {
  Ast* ast;
  int i;
  float f;
}

%token <i> INT
%token <f> FLOAT
%token EOL
%type <ast> exp 

%left ADD MINUS
%left TIMES DIVISION
%nonassoc UMINUS
%right POW
%token LP RP


%start calclist
%%

calclist 
       : calclist exp EOL {
           std::cout << "= " << eval($2) << std::endl;
           makeGraph($2);
           treeFree($2);
           std::cout << "> ";
	   type_of_number = true;
         }
       | calclist EOL {type_of_number = true;}
       | {type_of_number = true;}
       ;

exp    : exp ADD exp { $$ = new AstAdd(++id, $1,$3); }
       | exp MINUS exp { $$ = new AstMinus(++id, $1,$3); }
       | exp TIMES exp { $$ = new AstTimes(++id, $1,$3); }
       | exp DIVISION exp { $$ = new AstDivision(++id, $1,$3); }
       | MINUS exp %prec UMINUS { $$ = new AstUMinus(++id, $2, NULL); }
       | exp POW exp { $$ = new AstPow(++id, $1,$3); }
       | LP exp RP {$$=$2;}
       | INT  { $$ = new AstNumber(++id, $1); }
       | FLOAT {$$ = new AstNumber(++id, $1); type_of_number=false;}
       ;

%%
