//  Declarations for an AST calculator
//  From "flex & bison", fb3-1, by John Levine
//  Adapted by Brian Malloy
#include <iostream>
#include <string>
#include <fstream>
#include <sstream>
#include <math.h>
using std::string;
extern void yyerror(const char*);
extern void yyerror(const char*, const char);

class Ast {
public:
  Ast(int ID) :  identifier(ID) {}
  virtual ~Ast() {}
  int getID() const { return identifier; }
   virtual Ast* getLeft() const { throw std::string("error1: No Left"); }
   virtual Ast* getRight() const { throw std::string("error2: No Right"); }
   virtual string getTreeLable() const { throw string("error3: no Lable"); }
   virtual int jiajianchengchu_int() const { throw string("error4: no plus") ; }
   virtual float jiajianchengchu_float() const {throw string("error4: no plus"); }
    
private:

  int identifier;
};

class AstNode : public Ast {
public:
  AstNode(int ID, Ast* l, Ast* r) : 
    Ast(ID), left(l), right(r) 
  {}
  virtual ~AstNode() {}
  virtual Ast* getLeft() const  { return left; }
  virtual Ast* getRight() const { return right; }
private:
  Ast *left;
  Ast *right;
};

class AstNumber : public AstNode {
public:
  AstNumber(int ID, int in) : AstNode(ID, NULL, NULL), int_number(in),float_number(in)  {} 
  AstNumber(int ID, float fn) : AstNode(ID, NULL, NULL), int_number(fn),float_number(fn) {} 
  virtual ~AstNumber() { }
  string getTreeLable() const {
    std::ostringstream ss;
    ss<<float_number;
    return ss.str();
}
  int jiajianchengchu_int() const { return int_number; }
 float jiajianchengchu_float() const {return float_number; }
 
private:
   int int_number;
   float float_number;
};

double eval(Ast*);   
void treeFree(Ast*); 

class AstAdd : public AstNode {
public:
  AstAdd(int ID,  Ast* l, Ast* r) :
  AstNode(ID, l, r) {}
  ~AstAdd(){
    free(this->getLeft());
    free(this->getRight());
  }
  string getTreeLable() const {return "plus";}
    
  int jiajianchengchu_int() const { return eval(this->getLeft()) + eval(this->getRight());}
 float jiajianchengchu_float() const { return eval(this->getLeft()) + eval(this->getRight());}
  
private:
  
};

class AstMinus : public AstNode {
public:
  AstMinus(int ID, Ast* l, Ast* r) :
  AstNode(ID,l,r) {}
  
  ~AstMinus(){
    free(this->getLeft());
    free(this->getRight());
  }
   string getTreeLable() const {return "minus";}
  int jiajianchengchu_int() const { return eval(this->getLeft()) - eval(this->getRight());}
  float jiajianchengchu_float() const { return eval(this->getLeft()) - eval(this->getRight());}
private:
  
};

class AstTimes : public AstNode {
public:
  AstTimes(int ID, Ast* l, Ast* r) :
  AstNode(ID,l,r) {}
  
  ~AstTimes(){
    free(this->getLeft());
    free(this->getRight());
  }
  string getTreeLable() const {return "times";}
  int jiajianchengchu_int() const { return eval(this->getLeft()) * eval(this->getRight());}
  float jiajianchengchu_double() const { return eval(this->getLeft()) * eval(this->getRight());}
private:
  
};


class AstDivision : public AstNode {
public:
  AstDivision(int ID,Ast* l, Ast* r) :
  AstNode(ID,l,r) {}
  
  ~AstDivision(){
    free(this->getLeft());
    free(this->getRight());
  }
  string getTreeLable() const {return "divided by";}
  int jiajianchengchu_int() const { return eval(this->getLeft()) / eval(this->getRight());}
  float jiajianchengchu_float() const { return eval(this->getLeft()) / eval(this->getRight());}
private:
  
};

class AstPow : public AstNode {
public:
  AstPow(int ID, Ast* l, Ast* r) :
  AstNode(ID,l,r) {}
  
  ~AstPow(){
    free(this->getLeft());
    free(this->getRight());
  }
  string getTreeLable() const {return "to the power of";}
  int jiajianchengchu_int() const { return pow(eval(this->getLeft()), eval(this->getRight()));}
  float jiajianchengchu_float() const { return pow(eval(this->getLeft()), eval(this->getRight()));}
private:
  
};


class AstUMinus : public AstNode {
public:
  AstUMinus(int ID, Ast* l,Ast*r) :
  AstNode(ID,l,r) {}
  
  ~AstUMinus(){
    free(this->getLeft());
  }
  string getTreeLable() const {return "negativation";}
  int jiajianchengchu_int() const { return -eval(this->getLeft()) ;}
  float jiajianchengchu_float() const { return -eval(this->getLeft()) ;}
private:
  
};


void makeGraph1(Ast*, std::fstream&);
void makeGraph2(Ast*, std::fstream&);
void makeGraph(Ast*);



