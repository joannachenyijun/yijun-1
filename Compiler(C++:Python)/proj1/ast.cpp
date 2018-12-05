// Companion source code for "flex & bison", published by O'Reilly
// helper functions for fb3-1
#  include <iostream>
#  include <stdlib.h>
#  include "ast.h"
#  include <math.h>

extern bool type_of_number; 

double eval(Ast *a) {
 
   if(type_of_number){
    return a->jiajianchengchu_int();
  
   }
else
{
  return a->jiajianchengchu_float();
}
}

void treeFree(Ast *a) {
  
    delete a;
 

  }


void makeGraph(Ast* node) {
  std::fstream output;
  output.open("graph.gv", std::ios::out);
  output << "digraph G {" << std::endl;
  makeGraph1(node, output); //creat nodes and edges
  makeGraph2(node,output); //set label
  output << "}" << std::endl;
  output.close();
}


void makeGraph1( Ast* node, std::fstream& output) {
  if ( node->getLeft()!=NULL )
  {
      output<<"  "<<"\" " << node->getID() <<"\""<<"->";
      output << "\" " << node->getLeft()->getID() << "\"" << std::endl;
      makeGraph1( node->getLeft(), output );
    }

    if ( node->getRight()!= NULL ) 
    {
      output << "   " << "\" " << node->getID() << "\"" << "->";
      output << "\" " << node->getRight()->getID() << "\"" << std::endl;
      makeGraph1( node->getRight(), output );
    }
  }
  


void makeGraph2(Ast* node, std::fstream& output){
  //if(node->getLeft()!=NULL || node->getRight()!=NULL )
    //{
      output << "   " << "\" " << node->getID() << "\"" << 
      " [label=" << "\"" << node->getTreeLable() << "\"" << 
      ",color=green, style=filled]" << std::endl; 
      if ( node->getLeft()!=NULL ){
      makeGraph2 (node->getLeft(),output);}
      if ( node->getRight()!=NULL ){
     makeGraph2(node->getRight(),output);}
    //
  
    }
  





