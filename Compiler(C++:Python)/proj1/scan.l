%option nodefault yylineno
%{
  extern void yyerror(const char *s);
  extern void yyerror(const char *s, const char ch);
#include "ast.h"
#include "parse.tab.hpp"
%}
EXP   ([Ee][-+]?[0-9]+)
%%
"+"   { return ADD;}
"-"   { return MINUS;}
"*"   { return TIMES;}
"/"   { return DIVISION;}
"**"  { return POW;}
"("   { return LP;}                          
")"        { return RP; }
[0-9]+  {yylval.i=atoi(yytext); return INT;}
[0-9]+"."[0-9]*{EXP}? |
"."?[0-9]+{EXP}? { yylval.f = atof(yytext); return FLOAT; }



\n         { return EOL; }
"//".*  
[ \t]      { } // ignore white space
.	         { yyerror("Mystery character ", yytext[0]); }
%%
int yywrap() {
  yylex_destroy();
  return 1;
}
